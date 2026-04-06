<?php
  
class Synchro {
     /* @brief Fonction qui permet de synchroniser
     *        les modules heatzy
     *        
     * @return false en cas d'erreur le nombre de modules synchroniser       
     */
//class Synchro
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
//class Synchro
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
      
        //$Nb_Add = 0;
      	$return['new'] = 0 ;
      	$return['update'] = 0 ;
        $aSearchDid = [] ; //Va stocker les DID trouvé (pour vérifier ceux qui ont disparus)
        foreach ($aDevices['devices'] as $DeviceNum => $aDevice) {
            $aSearchDid[] = $aDevice['did'] ;
            $eqLogic = eqLogic::byLogicalId( $aDevice['did'] , 'heatzy', false);
            if (! is_object($eqLogic)) {   /// Creation des dids inexistants
                $eqLogic = new heatzy();
            	$eqLogic->setIsVisible(1);
                  //$Nb_Add++ ;
              	  $return['new']++ ;
            }
          	else
              $return['update']++ ;
            
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
      	$return['delete'] = 0 ;
        if( $return['new'] > 0)
            log::add('heatzy', 'info', $return['new'].' module(s) Heatzy ajouté(s) - '.count($aDevices ['devices']).'  module(s) Heatzy rattaché(s) au compte');
        log::add('heatzy', 'debug', __METHOD__.'(ln '.__LINE__.')'.' '.$return['new'].' module(s) Heatzy ajouté(s) - '.count($aDevices ['devices']).'  module(s) Heatzy rattaché(s) au compte');
        //message::add("Heatzy", 'Synchronistation de '. count($aDevices ['devices']).' module(s) Heatzy');
      
        // Recherche des équipements qui ne sont plus rattachés au compte
        foreach (eqLogic::byType('heatzy') as $heatzy) {
            if( !in_array($heatzy->getLogicalId() , $aSearchDid ) ){
                if( $heatzy->getIsEnable() == 1 ){
                    $heatzy->setIsEnable(0);
                    $heatzy->setIsVisible(0) ;
                    $heatzy->checkAndUpdateCmd('IsOnLine', 0 );   
                  	$heatzy->save();

                    $heatzy->setStatus('timeout','1');
                    log::add('heatzy', 'error', 'Le module -'.$heatzy->getName().'- ('.$heatzy->getLogicalId().') n est plus rattaché au compte. Il est maintenant désactivé et non visible (mais pas supprimé)' );   
                  	$return['delete']++ ;
                }
            }
        }
        //log::add('heatzy', 'debug', 'array_diff='.var_export( array_diff(eqLogic::byType('heatzy'), $aDevices ['devices']), true) );
      
        cache::set( 'Heatzy_Synchronize' , 0) ;
      
        //return count($aDevices['devices']);
      //return count($aDevices['devices']);
        return $return ;
    }
    
    /**
     * @brief Fonction qui permet de synchroniser
     *        les modules heatzy
     *        
     * @return false en cas d'erreur le nombre de modules synchroniser       
     */
//class Synchro
    public static function SynchronizeByLearning(  ) {

      	$tab_Acknow = self::LireJSON( '_Acknow' ) ;
        if( $tab_Acknow === false) return false ;      
        $eqLogics = eqLogic::byType('heatzy'); // récup tous les équipements heatzy
        $return['cmd'] = 0 ;
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
                        if( $eqLogic->CreateCmd( $cmd_a_creer ) == true )
                            $return['cmd']++ ;
                    } //foreach ($tab_Acknow[$key]
                } //if( isset( $tab_Acknow
            } //foreach($aRep['attr']
        } //foreach ($eqLogics
        //message::add("Heatzy", 'Etape 2/3 : Commandes créées par lecture et reconneconnaissance de json de retour' );
      	log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.' Etape 1/2 : Commandes créées par lecture et reconneconnaissance de json de retour' );

      
        $tab_Learn = self::LireJSON( '_Learn' ) ;
        if( $tab_Learn === false) return false ;
        $eqLogics = eqLogic::byType('heatzy'); // récup tous les équipements heatzy
        foreach ($eqLogics as $eqLogic) {
            //log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.') $key='.var_export( $eqLogic->getCmd( 'action' )->getLogicalId() , true) ) ;

            foreach ($tab_Learn as $key => $attr) {
                if( self::CmdsAllPresent( $attr['prerequis'] , $eqLogic ) and !self::CmdsAllPresent( $attr['create'] , $eqLogic )  ){
                  	// Si les cmd prérequis sont présentes et que toutes les commandes cibles ne sont pas présentes
                    log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.') '.$eqLogic->getName().' ON VA CHERCHER ' ) ;

                    $VerifOrdre = self::CheckOrders( $eqLogic , $attr['consigne'] , $attr['after'] , $attr['verif'] ) ;
                    if( $VerifOrdre == true  ){
                        foreach ($attr['create'] as $cmd_a_creer){
                            log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.') '.$eqLogic->getName().' Création commande '.$cmd_a_creer ) ;
                            if($eqLogic->CreateCmd( $cmd_a_creer ) == true )
                                $return['cmd']++ ;
                        }
                    }
                }
                else
                   log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.') '.$eqLogic->getName().' Prérequis et cible KO '.$key ) ;
                   
            } //foreach $tab_Learn
          
        } //foreach $eqLogics
        message::add("Heatzy", 'Etape 2/2 : Commandes créées par essai et erreur' );
        log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.' Etape 3/3 : Commandes créées par essai et erreur' );

        //sleep( 1 ) ;
        message::add("Heatzy", 'Apprentissage terminé' );
        //sleep( 1 ) ;
        //return '99' ;
        return $return ;
    }
  
    /**
     * @brief Fonction qui permet de synchroniser
     *        les modules heatzy
     *        
     * @return false en cas d'erreur le nombre de modules synchroniser       
     */
//class Synchro
    public static function CmdsAllPresent( $prerequis , $eqLogic ) {
              $CmdsAllPresent = true ;
              foreach ($prerequis as $prerequis)
                  if ( !is_object( $eqLogic->getCmd(null ,$prerequis ) ) ) {
                    $CmdsAllPresent = false ;
                    break ;
                  }
        return $CmdsAllPresent ;
    }
  
    /**
     * @brief Fonction qui permet de synchroniser
     *        les modules heatzy
     *        
     * @return false en cas d'erreur le nombre de modules synchroniser       
     */
//class Synchro
    public static function MajAllCmds( $TypeMaj ) {
      	log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.': $TypeMaj='.$TypeMaj );
        $eqLogics = eqLogic::byType('heatzy'); // récup tous les équipements heatzy
        foreach ($eqLogics as $eqLogic) {
            foreach ($eqLogic->getCmd() as $cmd) {
                if( $TypeMaj == 'order' ) $eqLogic->CreateCmd( $cmd->getLogicalId() , true  , false ) ;
                if( $TypeMaj == 'name'  ) $eqLogic->CreateCmd( $cmd->getLogicalId() , false , true  ) ;
            } //foreach getCmd()          
        } //foreach $eqLogics
        return true ;
    }  
    /**
     * @brief Fonction qui permet de synchroniser
     *        les modules heatzy
     *        
     * @return false en cas d'erreur le nombre de modules synchroniser       
     */
//class Synchro
    public static function CheckOrders( $eqLogic , $consigne , $after , $verif ) {

        // On tente de mettre la valeur - Appel API pour SET $valeur
        //log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.': '.$eqLogic->getName().' Consigne='.var_export($consigne, true) );
        $ResultSet = HttpGizwits::SetConsigne( $eqLogic->getLogicalId(), array( 'attrs' => $consigne  ) );

        if( $ResultSet['error_code'] == '9025' ){
            log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.': '.$eqLogic->getName().' - '.key($consigne).'=>'.$consigne[ key($consigne) ].' SET error 9025 attribut invalide');
            return false;
        }

        if( $ResultSet['error_code'] == ''){
            if( !$verif ){
                log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.': '.$eqLogic->getName().' - '.key($consigne).'=>'.$consigne[ key($consigne) ].' OK Pas besoin de verif (=>'.$ResultGet['attr'][ key($consigne)].')' );
                //return true ;
            }
            else{
                sleep(2); // Attente 2sec
                // Appel API pour analyser le changement ou non de consigne
                $ResultGet = HttpGizwits::GetConsigne( $eqLogic->getLogicalId() ) ;
                if( $ResultGet['error_code'] == ''){
                    if( $ResultGet['attr'][key($consigne)] == $consigne[key($consigne)] ){
                        log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.': '.$eqLogic->getName().' - '.key($consigne).'=>'.$consigne[ key($consigne) ].' - SET valorisé avec succes '.$ResultGet['attr'][key($consigne)]);
                        //return true ;
                    }
                    else{
                        log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.': '.$eqLogic->getName().' - '.key($consigne).'=>'.$consigne[ key($consigne) ].' - SET valeur autre (KO-'.$ResultGet['attr'][key($consigne)].')');
                        return false;
                    }
                }
                else{
                    log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.': '.$eqLogic->getName().' error_code1 GET='.$ResultSet['error_code'].' - '.$ResultSet['detail_message']);
                    return false;
                }
            }
        }
        else{
            log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.': '.$eqLogic->getName().' error_code2 SET='.$ResultSet['error_code'].' - '.$ResultSet['detail_message']);
            return false;
        } //  if( $ResultSet['error_code'] == ''){


        // On remet l'ordre initial
        if( !empty($after) ){
            sleep(2);
            $ResultSet = HttpGizwits::SetConsigne( $eqLogic->getLogicalId(), array( 'attrs' => $after  ) );
            if( $ResultSet['error_code'] != '' )
                log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.': '.$this->getName().' error_code3 SET='.$ResultSet['error_code'].' - '.$ResultSet['detail_message']);
            else
                log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.': '.$eqLogic->getName().' - '.key($after).'=>'.$after[ key($after) ].' - SET after valorisé avec succes');
        }

        return true ;
    }
  
    /**
     * @brief Fonction qui permet de synchroniser
     *        les modules heatzy
     *        
     * @return false en cas d'erreur le nombre de modules synchroniser       
     */
//class Synchro
    public static function CreateCmdByLearning(  ) {

        return true ;
    }
  
    /**
     * @brief Fonction qui permet de synchroniser
     *        les modules heatzy
     *        
     * @return false en cas d'erreur le nombre de modules synchroniser       
     */
//class Synchro
    public static function StatsHeatzy(  ) {
        $eqLogics = eqLogic::byType('heatzy'); // récup tous les équipements heatzy
        foreach ($eqLogics as $eqLogic) {
            $aRep = HttpGizwits::GetConsigne( $eqLogic->getLogicalId() ) ;
            if( $aRep != false ){
                // Anonymisation et ajout d'info
                $aRep['did'] = $eqLogic->getId() ; //unset($aRep['did']) ;//$aRep['did'] = 'xxxxxxxxxxxx' ;
              	unset($aRep['updated_at']) ;
                $aRep['product_key'] = $eqLogic->getConfiguration('product_key', '') ;
                $aRep['product_name'] = $eqLogic->getConfiguration('product', '') ;
				$aRep['API'] = config::byKey('API_Type','heatzy','') ;

                //log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.': SetStatsHeatzy...'.$eqLogic->getLogicalId().'-'.json_encode($aRep));

                HttpGizwits::SetStatsHeatzy( json_encode( $aRep ) ) ;
            }
            sleep(1) ;
        }
        return true ;
    }

  
  
} // Fin class Synchro