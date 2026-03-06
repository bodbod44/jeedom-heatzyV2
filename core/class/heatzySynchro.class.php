<?php
  
class Synchro {
     /* @brief Fonction qui permet de synchroniser
     *        les modules heatzy
     *        
     * @return false en cas d'erreur le nombre de modules synchroniser       
     */
//class Autre
    public static function LireJSON( $json_name ) {
        $json = file_get_contents(__DIR__.'/'.$json_name.'.json') ;
        if( $json ) {
            $tab = json_decode( $json , true );
            if( !$tab ){
                log::add('heatzy', 'error',  __METHOD__.'(ln '.__LINE__.') Problème decode '.$json_name.'.json' ) ;
                return false ;
            }
        }
        else{
            log::add('heatzy', 'error',  __METHOD__.'(ln '.__LINE__.') Problème lecture '.$json_name.'.json' ) ;
            return false ;
        }
        return $tab ;
    }

    /**
     * @brief Fonction qui permet de synchroniser
     *        les modules heatzy
     *        
     * @return false en cas d'erreur le nombre de modules synchroniser       
     */
//class heatzy extends eqLogic
    public static function SynchronizeHeatzy( $force = false ) {
            
          log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.' : $force='.$force);
      
        if( !cache::exist('Heatzy_Synchronize') ) cache::set( 'Heatzy_Synchronize' , 1) ;
        cache::set( 'Heatzy_Synchronize' , strtotime(date("Y-m-d H:i:s")) ) ;
      
        /// Login + creation du cron
        if( heatzy::Login() === false ){
            log::add('heatzy', 'warning',  __METHOD__.'(ln '.__LINE__.')'.' : heatzy::Login - impossible de se connecter à : '.HttpGizwits::$UrlGizwits);
            return false;
        }
            
        $UserToken = config::byKey('UserToken','heatzy','none');   
      
        /// Bindings
        $aDevices = HttpGizwits::Bindings($UserToken);      
      
        if($aDevices === false) {
            log::add('heatzy', 'warning',  __METHOD__.'(ln '.__LINE__.')'.' : HttpGizwits::Bindings - impossible de se connecter à : '.HttpGizwits::$UrlGizwits);
            return false;
        }
        
          log::add('heatzy', 'debug', __METHOD__.'(ln '.__LINE__.')'.' '.count($aDevices ['devices']).'  module(s) trouvé');
        //log::add('heatzy', 'debug', __METHOD__.'(ln '.__LINE__.')'.' $aDevice :'.var_export($aDevices, true));
      
        $Nb_Add = 0;
        $aSearchDid = [] ; //Va stocker les DID trouvé (pour vérifier ceux qui ont disparus)
        foreach ($aDevices['devices'] as $DeviceNum => $aDevice) {
            $aSearchDid[] = $aDevice['did'] ;
            $eqLogic = eqLogic::byLogicalId( $aDevice['did'] , 'heatzy', false);
            if (! is_object($eqLogic)) {   /// Creation des dids inexistants
                $eqLogic = new heatzy();
            	$eqLogic->setIsVisible(1);
                  $Nb_Add++ ;
            }
            $eqLogic->setEqType_name('heatzy');
            $eqLogic->setLogicalId($aDevice['did']);

            if($aDevice['is_disabled'] === 'false')
                $eqLogic->setIsEnable(0);
            else
                $eqLogic->setIsEnable(1);
            
            if (empty($aDevice['dev_alias']))
                $eqLogic->setName(strtoupper($aDevice['mac']));
            else
                $eqLogic->setName($aDevice['dev_alias']);
            
            if(isset($aDevice['mac']))
                $eqLogic->setConfiguration('mac',implode(':',str_split($aDevice['mac'], 2)));

            if(isset($aDevice['product_name']))
                $eqLogic->setConfiguration('product',$aDevice['product_name']);

            if(isset($aDevice['product_key']))
                $eqLogic->setConfiguration('product_key',$aDevice['product_key']);
          
            /*
            /// Retourne les informations sur le produit
            $aProductInfo = HttpGizwits::GetProduitInfo($aDevice['product_key']) ;
            
            if (isset ($aProductInfo['name']))
                $eqLogic->setConfiguration('product',$aProductInfo['name']);
            if (isset ($aProductInfo['product_key']))
                        $eqLogic->setConfiguration('product_key',$aProductInfo['product_key']);

            if  ( strcmp( $aProductInfo['name'] , "INEA" ) === 0 )
                 $eqLogic->setConfiguration('heatzytype','flam');
            else if ( strncmp ( $aProductInfo['name'] , "Flam" , 4 ) === 0 )
                 $eqLogic->setConfiguration('heatzytype','flam');
            else
                 $eqLogic->setConfiguration('heatzytype','pilote');*/
          
            /// Si connecté ou pas
            if( $aDevice['is_online'] == 'true')
                $eqLogic->checkAndUpdateCmd('IsOnLine', 1 );
            else
                $eqLogic->checkAndUpdateCmd('IsOnLine', 0 );
          
              $eqLogic->save();
          
              // A mettre après le save (car le save met le statut à 0)
            if( $aDevice['is_online'] == 'true' ){
                $eqLogic->setStatus('timeout','0');
            }
            else{
                $eqLogic->setStatus('timeout','1');
            }
                          
            // mise à jour du did
            if ($eqLogic->getIsEnable() == 1 && $eqLogic->getStatus('timeout') == 0 ) {
                  // Ne pas faire si timeout (car l'update va remettre reinit le timeout)
              $eqLogic->InitCmds()  ;
              $eqLogic->updateHeatzyDid( null , $force );
              
            }
          
        } // foreach
        
        //log::add('heatzy', 'info', 'Synchronistation de '. count($aDevices ['devices']).' module(s) Heatzy');
        if( $Nb_Add > 0)
            log::add('heatzy', 'info', $Nb_Add.' module(s) Heatzy ajouté(s) - '.count($aDevices ['devices']).'  module(s) Heatzy rattaché(s) au compte');
        log::add('heatzy', 'debug', __METHOD__.'(ln '.__LINE__.')'.' '.$Nb_Add.' module(s) Heatzy ajouté(s) - '.count($aDevices ['devices']).'  module(s) Heatzy rattaché(s) au compte');
        //message::add("Heatzy", 'Synchronistation de '. count($aDevices ['devices']).' module(s) Heatzy');
      
        // Recherche des équipements qui ne sont plus rattachés au compte
        foreach (eqLogic::byType('heatzy') as $heatzy) {
            if( !in_array($heatzy->getLogicalId() , $aSearchDid ) ){
                if( $heatzy->getIsEnable() == 1 ){
                    $heatzy->setIsEnable(0);
                    $heatzy->setIsVisible(0) ;
                    $heatzy->save();
                    $heatzy->checkAndUpdateCmd('IsOnLine', 0 );   
                    $heatzy->setStatus('timeout','1');
                    log::add('heatzy', 'error', 'Le module -'.$heatzy->getName().'- ('.$heatzy->getLogicalId().') n est plus rattaché au compte. Il est maintenant désactivé et non visible (mais pas supprimé)' );   
                }
            }
        }
        //log::add('heatzy', 'debug', 'array_diff='.var_export( array_diff(eqLogic::byType('heatzy'), $aDevices ['devices']), true) );
      
        cache::set( 'Heatzy_Synchronize' , 0) ;
      
        return count($aDevices['devices']);
    }
    
    /**
     * @brief Fonction qui permet de synchroniser
     *        les modules heatzy
     *        
     * @return false en cas d'erreur le nombre de modules synchroniser       
     */
//class heatzy extends eqLogic
    public static function SynchronizeByLearning(  ) {
        log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')' );

        message::add("Heatzy", 'Lancement de l\'apprentissage' );

        $res = Synchro::SynchronizeHeatzy() ;
        log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.': Synchronize = '.$res );
        if( $res == false ){
            log::add('heatzy', 'error',  __METHOD__.'(ln '.__LINE__.') Problème lors de la synchronisation (Synchronize)' ) ;
            return false ;
        }
        else
            message::add("Heatzy", 'Etape 1/3 : '.$res.' modules synchronisés avec le compte Heatzy' );
        log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.' Etape 1/3 : '.$res.' modules synchronisés avec le compte Heatzy' );


      	$tab_Acknow = self::LireJSON( '_Acknow' ) ;
        if( $tab_Acknow === false) return false ;      
        $eqLogics = eqLogic::byType('heatzy'); // récup tous les équipements heatzy
        foreach ($eqLogics as $eqLogic) {
            $aRep = HttpGizwits::GetConsigne( $eqLogic->getLogicalId() ) ;

            if($aRep === false){
                log::add('heatzy', 'warning',  __METHOD__.'(ln '.__LINE__.')'.' : impossible de se connecter à:'.HttpGizwits::$UrlGizwits);
                return false;
            }
            else if(isset($aRep['error_message']) && isset($aRep['error_code'])) {
                log::add('heatzy', 'error',  __METHOD__.'(ln '.__LINE__.')'.' : '.$this->getLogicalId().' - '.$aRep['error_code'].' - '.$aRep['error_message'].' - '.$aRep['detail_message']);
                return false;
            }
            
            foreach($aRep['attr'] as $key => $attr) {
                if( isset( $tab_Acknow[ $key ] ) ){
                    //log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.') '.$eqLogic->getName().' TROUVE $attr='.$key.'=>'.$attr.'-'.$tab_Acknow[ $key ] ) ;
                    foreach ($tab_Acknow[$key] as $cmd_a_creer) {
                        $eqLogic->CreateCmd( $cmd_a_creer ) ;
                    } //foreach ($tab_Acknow[$key]
                } //if( isset( $tab_Acknow
            } //foreach($aRep['attr']
        } //foreach ($eqLogics
        message::add("Heatzy", 'Etape 2/3 : Commandes créées par lecture et reconneconnaissance de json de retour' );
      	log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.' Etape 2/3 : Commandes créées par lecture et reconneconnaissance de json de retour' );

      
        $tab_Learn = self::LireJSON( '_Learn' ) ;
        if( $tab_Learn === false) return false ;
        $eqLogics = eqLogic::byType('heatzy'); // récup tous les équipements heatzy
        foreach ($eqLogics as $eqLogic) {
            foreach ($tab_Learn as $Learn) {
                log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.') Learn='.$Learn ) ;
            } //foreach ($tab_Acknow[$key]
            //$eqLogic->getLogicalId()
            //message::add("Heatzy", 'Etape 2/5 : Module '.$eqLogic->getLogicalId() );
            //Apprentissage
        }
        message::add("Heatzy", 'Etape 3/3 : Commandes créées par essai et erreur' );
      log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.' Etape 3/3 : Commandes créées par essai et erreur' );

        sleep( 1 ) ;
        message::add("Heatzy", 'Apprentissage terminé' );
        sleep( 1 ) ;
        return '99' ;
    }
  
} // Fin class Synchro