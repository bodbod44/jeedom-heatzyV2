<?php
  
class heatzyCmd extends cmd {
    /*     * *************************Attributs****************************** */


    /*     * ***********************Methode static*************************** */


    /*     * *********************Methode d'instance************************* */

    /*
     * Non obligatoire permet de demander de ne pas supprimer les commandes 
     * même si elles ne sont pas dans la nouvelle configuration de l'équipement envoyé en JS
     * public function dontRemoveCmd() {
     * return true;
     * }
     */
// class heatzyCmd extends cmd
    public function execute($_options = array()) {
        log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.' : Commande execute : '.$this->getEqLogic()->getName().' - '.$this->getLogicalId().' ('.$this->getId().')');  
      
        if( HttpGizwits::$DebugExport ){
            //var_export($col, true)
            log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.' : $_options1 : '.$_options ); 
            //log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.' : $_options2 : '.json_decode($_options, true) ); 
            log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.' : $_options3 : '.var_export($_options, true) );
        }      
      
      
        $Result = array();
        
        /// Lecture du token
      
        if ($this->getLogicalId() == 'refresh') {
            $this->getEqLogic()->updateHeatzyDid();
        }
        else if($this->getType() == 'info' ) {
              return $this->getValue();
        }
        else if($this->getType() == 'action' ) {
            
            $eqLogic = $this->getEqLogic();
            //log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.' : '.$eqLogic->getName().' - LogicalId='.$this->getLogicalId().' ('.$this->getId().')');
            
            $Consigne = '' ;
            $ForUpdate = '' ;
            if ($this->getLogicalId() == 'plugzyon') {        
                $Consigne = array( 'attrs' => array ( 'on_off' => 1 )  );
                $ForUpdate = 1 ;
            }
            else if ($this->getLogicalId() == 'plugzyoff') {              
                $Consigne = array( 'attrs' => array ( 'on_off' => 0 )  );
                $ForUpdate = 0 ;
            }
            else if ($this->getLogicalId() == 'ProgOn') {
                if( $eqLogic->getConfiguration('product', '') == 'Heatzy' || $eqLogic->getConfiguration('product', '') == 'Flam_Week2'){
                    $eqLogic->GestProg(true);
                }
                else {
                    $Consigne = array( 'attrs' => array ( 'timer_switch' => 1 )  );
                }
                $ForUpdate = 1 ;
            }
            else if ($this->getLogicalId() == 'ProgOff') {
                if( $eqLogic->getConfiguration('product', '') == 'Heatzy' || $eqLogic->getConfiguration('product', '') == 'Flam_Week2'){
                    $eqLogic->GestProg(false);
                }
                else {
                    $Consigne = array( 'attrs' => array ( 'timer_switch' => 0 )  );
                }
                $ForUpdate = 0 ;
            }
            else if ($this->getLogicalId() == 'LockOn') {
                $Consigne = array( 'attrs' => array ( 'lock_switch' => 1 )  );
                $ForUpdate = 1 ;
            }
            else if ($this->getLogicalId() == 'LockOff') {
                $Consigne = array( 'attrs' => array ( 'lock_switch' => 0 )  );
                $ForUpdate = 0 ;
            }
            else if ($this->getLogicalId() == 'Lock_C_On') {
                $Consigne = array( 'attrs' => array ( 'LOCK_C' => 1 )  );
                $ForUpdate = 1 ;
            }
            else if ($this->getLogicalId() == 'Lock_C_Off') {
                $Consigne = array( 'attrs' => array ( 'LOCK_C' => 0 )  );
                $ForUpdate = 0 ;
            }
            else if ($this->getLogicalId() == 'WindowSwitchOn') {
                $Consigne = array( 'attrs' => array ( 'window_switch' => 1 )  );
                $ForUpdate = 1 ;
            }
            else if ($this->getLogicalId() == 'WindowSwitchOff') {
                $Consigne = array( 'attrs' => array ( 'window_switch' => 0 )  );
                $ForUpdate = 0 ;
            }
            else if ($this->getLogicalId() == 'derog_off') {
                $Consigne = array( 'attrs' => array ( 'derog_mode' => 0 )  ); // 0 : pas de dérogation
                $ForUpdate = 0 ;
            }
            else if ($this->getLogicalId() == 'derog_vacances') {
                  isset( $_options['slider'] ) ? $delai = intval( $_options['slider'] ) : $delai = 1 ;
                $Consigne = array( 'attrs' => array ( 'derog_mode' => 1 , 'derog_time' => $delai , 'mode' => 'fro' )  ); // 1 : mode vacances
                $ForUpdate = $delai ;
            }
            else if ($this->getLogicalId() == 'derog_boost') {
                  isset( $_options['slider'] ) ? $delai = intval( $_options['slider'] ) : $delai = 60 ;
                $Consigne = array( 'attrs' => array ( 'derog_mode' => 2 , 'derog_time' => $delai , 'mode' => 'cft' )  ); // 2 : mode boost
                $ForUpdate = $delai ;
            }
            else if ($this->getLogicalId() == 'derog_presence') {
                $Consigne = array( 'attrs' => array ( 'derog_mode' => 3 )  ); // 3 : détection de presence
                $ForUpdate = 3 ;
            }
            else if ($this->getLogicalId() == 'derog_time') {
                $Consigne = array( 'attrs' => array ( 'derog_time' => 0 )  );
                $ForUpdate = 0 ;
            }
            else if ($this->getLogicalId() == 'cft_temp_consigne') {
                //log::add('heatzy', 'debug', __METHOD__.'(ln '.__LINE__.')'.' '.$this->getLogicalId() . ' ForUpdate - '.$this->getConfiguration('infoName').'=>'.$ForUpdate );
                //$this->getConfiguration('tempHL',false)
                isset( $_options['slider'] ) ? $consigne = floatval( $_options['slider'] ) : $consigne = 0 ;

                log::add('heatzy', 'debug', __METHOD__.'(ln '.__LINE__.')'.' '.$this->getLogicalId() . ' $consigne='.$consigne );     
                if( $this->getConfiguration('tempHL',false) ){
                    $tempBIN = str_pad( decbin($consigne * 10),  16, "0", STR_PAD_LEFT) ;
                    $tempH = bindec(substr( $tempBIN , 0 , 8 )) ;
                    $tempL = bindec(substr( $tempBIN , 8 )) ;
                    //log::add('heatzy', 'debug', __METHOD__.'(ln '.__LINE__.')'.' '.$this->getLogicalId() . ' $tempBIN='.$tempBIN.'-'.$tempH.'-'.$tempL.'-'.bindec($tempH).'-'.bindec($tempL) );                  
                    $Consigne = array( 'attrs' => array ( 'cft_tempH' => $tempH , 'cft_tempL' => $tempL )  );
                }
                else{
                    $Consigne = array( 'attrs' => array ( 'cft_temp' => $consigne * 10 )  );
                }
                $ForUpdate = $consigne ;
            }
            else if ($this->getLogicalId() == 'eco_temp_consigne') {
                isset( $_options['slider'] ) ? $consigne = floatval( $_options['slider'] ) : $consigne = 0 ;
              
                if( $this->getConfiguration('tempHL',false) ){
                    $tempBIN = str_pad( decbin($consigne * 10),  16, "0", STR_PAD_LEFT) ;
                    $tempH = bindec(substr( $tempBIN , 0 , 8 )) ;
                    $tempL = bindec(substr( $tempBIN , 8 )) ;
                    //log::add('heatzy', 'debug', __METHOD__.'(ln '.__LINE__.')'.' '.$this->getLogicalId() . ' $tempBIN='.$tempBIN.'-'.$tempH.'-'.$tempL.'-'.bindec($tempH).'-'.bindec($tempL) );                  
                    $Consigne = array( 'attrs' => array ( 'eco_tempH' => $tempH , 'eco_tempL' => $tempL )  );
                }
                else{
                    $Consigne = array( 'attrs' => array ( 'eco_temp' => $consigne * 10 )  );
                }
                $ForUpdate = $consigne ;
            }
            else if( in_array($this->getLogicalId() , heatzy::$_HeatzyMode ) ) {
              
                $Mode = array_keys(heatzy::$_HeatzyMode, $this->getLogicalId());
              
                //log::add('heatzy', 'debug', __METHOD__.'(ln '.__LINE__.')'.' '.$this->getLogicalId() . ' mode = '. var_export($Mode, true));
              
                if( $eqLogic->getConfiguration('product', 'Heatzy') == 'Heatzy') {    /// Premiere version du module pilote
                    $Consigne = array( 'raw' => array(1, 1, $Mode[0]) ) ;
                }
                else {
                    switch($Mode[0])
                    {
                    case 0:
                       $Mode = 'cft'; break;
                    case 1:
                       $Mode = 'eco'; break;
                    case 2:
                       $Mode = 'fro'; break;
                    case 3:
                       $Mode = 'stop'; break;
                    case 4:
                       $Mode = 'cft1'; break;
                    case 5:
                       $Mode = 'cft2'; break;
                    }
                  
                    $Consigne = array( 'attrs' => array ( 'mode' => $Mode )  );
                }
                $ForUpdate = '' ;
            }
            else{
                log::add('heatzy', 'error',  __METHOD__.'(ln '.__LINE__.')'.' : Commande inconnue : '.$this->getEqLogic()->getName().' - '.$this->getLogicalId().' ('.$this->getId().')');
            }/// Le mode
              
            
            if( $Consigne != '' ){
              	if( config::byKey('API_Type','heatzy','REST') == 'REST' ){
                    log::add('heatzy', 'debug',  __METHOD__.'(ln '.__LINE__.')'.' :$Consigne != null : ');
                    //$Result = HttpGizwits::SetConsigne($UserToken, $eqLogic->getLogicalId(), $Consigne);
                    $Result = HttpGizwits::SetConsigne( $eqLogic->getLogicalId(), $Consigne);

                    if($Result === false) {
                        log::add('heatzy', 'error',  __METHOD__.'(ln '.__LINE__.')'.' : '.$this->getEqLogic()->getName().' - '.$this->getLogicalId().' - impossible de se connecter à:'.HttpGizwits::$UrlGizwits);
                        return false;
                    }
                    else{
                        /// Si une erreur de communication
                        if(isset($Result['error_message']) && isset($Result['error_code'])) {
                            log::add('heatzy', 'error',  __METHOD__.'(ln '.__LINE__.')'.' : '.$this->getEqLogic()->getName().' - '.$this->getLogicalId().' - '.$Result['error_code'].' - '.$Result['error_message'].' - '.$Result['detail_message']);

                            if( $Result['error_code'] == '9017' || $Result['error_code'] == '9042' ){
                                // 9017 = Détaché du compte
                                // 9042 = Offline
                                $eqLogic->setStatus('timeout','1');
                                $eqLogic->checkAndUpdateCmd('IsOnLine', 0 );
                            }
                            return false;
                        }
                        else if($ForUpdate != ''){
                              log::add('heatzy', 'debug', __METHOD__.'(ln '.__LINE__.')'.' '.$this->getLogicalId() . ' ForUpdate - '.$this->getConfiguration('infoName').'=>'.$ForUpdate );
                            $eqLogic->checkAndUpdateCmd( $this->getConfiguration('infoName') , $ForUpdate ) ;
                            $eqLogic->checkAndUpdateCmd('IsOnLine', 1 );
                        }
                    } // $Result === false
                } // if REST
				else{
                    // Envoi au demon
                    log::add('heatzy', 'debug', __METHOD__.'(ln '.__LINE__.')'.' '.$this->getLogicalId() . ' Envoi au demon : '.json_encode($Consigne) );
                    $this->getEqLogic()->sendToDaemon( 'execute' , $this->getEqLogic()->getLogicalId() , $Consigne ) ; 
                } // REST
            } // if $Consigne != ''
            
          if( config::byKey('API_Type','heatzy','REST') == 'REST' ){
            /// Mise à jour de l'état
            sleep(1); // tempo de 1sec pour laisser le temps a l'API de le prendre en compte et le restituer
            $this->getEqLogic()->updateHeatzyDid();
          }
            
        } /// Fin action
        $mc = cache::byKey('heatzyWidgetmobile' . $this->getEqLogic()->getId());
        $mc->remove();
        $mc = cache::byKey('heatzyWidgetdashboard' . $this->getEqLogic()->getId());
        $mc->remove();

        //$this->getEqLogic()->toHtml('mobile');
        //$this->getEqLogic()->toHtml('dashboard');
        //$this->getEqLogic()->refreshWidget();
        log::add('heatzy', 'debug', __METHOD__.'(ln '.__LINE__.')'.' '.$this->getLogicalId() . ' FIN EXECUTE' );
        return true;
    }

    /*     * **********************Getteur Setteur*************************** */
}