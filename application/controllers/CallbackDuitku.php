<?php defined('BASEPATH') or exit('No direct script access allowed');

class CallbackDuitku extends CI_Controller {

    public function  __construct() {
        parent::__construct();
        //$this->load->model('ci_ext_model', 'ci_ext');
        //$ci_ext = $this->ci_ext->ciext();
        //if (!$ci_ext) {
        //    redirect(gagal);
       // }
       // if ($this->session->userdata('user_name') == NULL && $this->session->userdata('password') == NULL) {
            //redirect(base_url() . "login");
        //}
       // $this->load->library('form_validation');
        $this->load->model('wallet_model', 'wlt');
        $this->load->model('customer_model', 'user');
    }
    
    public function confirm_pembayaran() {
      
            $apiKey = 'bd9cdd0853a015722959816d0aaf6c31'; // Your api key
            $merchantCode = isset($_POST['merchantCode']) ? $_POST['merchantCode'] : null; 
            $amount = isset($_POST['amount']) ? $_POST['amount'] : null; 
            $merchantOrderId = isset($_POST['merchantOrderId']) ? $_POST['merchantOrderId'] : null; 
            $productDetail = isset($_POST['productDetail']) ? $_POST['productDetail'] : null; 
            $additionalParam = isset($_POST['additionalParam']) ? $_POST['additionalParam'] : null; 
            $paymentMethod = isset($_POST['paymentCode']) ? $_POST['paymentCode'] : null; 
            $resultCode = isset($_POST['resultCode']) ? $_POST['resultCode'] : null; 
            $merchantUserId = isset($_POST['merchantUserId']) ? $_POST['merchantUserId'] : null; 
            $reference = isset($_POST['reference']) ? $_POST['reference'] : null; 
            $signature = isset($_POST['signature']) ? $_POST['signature'] : null; 
            $vaNumber 			= isset($_POST['vaNumber']) ? $_POST['vaNumber'] : null; 
            $issuer_name 		= isset($_POST['issuer_name']) ? $_POST['issuer_name'] : null; // Hanya untuk ATM Bersama
            $issuer_bank 		= isset($_POST['issuer_bank']) ? $_POST['issuer_bank'] : null; // Hanya untuk ATM Bersama

            //write log
		    file_put_contents('duitku_callback_log.txt', "*** Logs virtual Account ***\r\n", FILE_APPEND | LOCK_EX);
			file_put_contents('duitku_callback_log.txt', "STATUS : " . $resultCode . "\r\n", FILE_APPEND | LOCK_EX);
			file_put_contents('duitku_callback_log.txt', "\r\n***************************\r\n\r\n", FILE_APPEND | LOCK_EX);
			file_put_contents('duitku_callback_log.txt', "*** " . date("Y-m-d H:i:s") . " ***\r\n", FILE_APPEND | LOCK_EX);
			file_put_contents('duitku_callback_log.txt', "merchantOrderId : " . $merchantOrderId . " \r\n", FILE_APPEND | LOCK_EX);
			file_put_contents('duitku_callback_log.txt', "amount : " . $amount . " \r\n", FILE_APPEND | LOCK_EX);
			file_put_contents('duitku_callback_log.txt', "merchantUserId : " . $merchantUserId . " \r\n", FILE_APPEND | LOCK_EX);
			file_put_contents('duitku_callback_log.txt', "merchantCode : " . $merchantCode . " \r\n", FILE_APPEND | LOCK_EX);
			file_put_contents('duitku_callback_log.txt', "productDetail : " . $productDetail . " \r\n", FILE_APPEND | LOCK_EX);
			file_put_contents('duitku_callback_log.txt', "additionalParam : " . $additionalParam . " \r\n", FILE_APPEND | LOCK_EX);
			file_put_contents('duitku_callback_log.txt', "resultCode : " . $resultCode . " \r\n", FILE_APPEND | LOCK_EX);
			file_put_contents('duitku_callback_log.txt', "signature : " . $signature . " \r\n", FILE_APPEND | LOCK_EX);
			file_put_contents('duitku_callback_log.txt', "paymentCode : " . $paymentMethod . " \r\n", FILE_APPEND | LOCK_EX);
			file_put_contents('duitku_callback_log.txt', "merchantUserId : " . $merchantUserId . " \r\n", FILE_APPEND | LOCK_EX);
			file_put_contents('duitku_callback_log.txt', "reference : " . $reference . " \r\n", FILE_APPEND | LOCK_EX);
			file_put_contents('duitku_callback_log.txt', "vaNumber : " . $vaNumber . " \r\n", FILE_APPEND | LOCK_EX);
			file_put_contents('duitku_callback_log.txt', "issuer_name : " . $issuer_name . " \r\n", FILE_APPEND | LOCK_EX);
			file_put_contents('duitku_callback_log.txt', "issuer_bank : " . $issuer_bank . " \r\n", FILE_APPEND | LOCK_EX);
			file_put_contents('duitku_callback_log.txt', "\r\n***************************\r\n\r\n", FILE_APPEND | LOCK_EX);            
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
            if(!empty($merchantCode) && !empty($amount) && !empty($merchantOrderId) && !empty($signature))
            {
                $params = $merchantCode . $amount . $merchantOrderId . $apiKey;
                $calcSignature = md5($params);
            
                if($signature == $calcSignature)
                {
                    
                    
                    if($resultCode == '00'){
                    
                                    $tempWallet = $this->wlt->getwalletbyOrderId($merchantOrderId);
                           
                                    $id_user = $tempWallet['id_user'] ;
                                    $id = $tempWallet['id']  ;
                            
                                    $token = $this->wlt->gettoken($id_user);
                                    $regid = $this->wlt->getregid($id_user);
                                    $saldo = $this->wlt->getsaldo($id_user);
                      
                                    
                                    $tokenmerchant = $this->wlt->gettokenmerchant($id_user);
                                    $topic = '' ;
                            
                                    if ($token == NULL and $tokenmerchant == NULL and $regid != NULL) {
                                        $topic = $regid['reg_id'];
                                    } else if ($regid == NULL and $tokenmerchant == NULL and $token != NULL) {
                                        $topic = $token['token'];
                                    } else if ($regid == NULL and $token == NULL and $tokenmerchant != NULL) {
                                        $topic = $tokenmerchant['token_merchant'];
                                    }
                            
                            
                                    $title = 'Topup success';
                                    $message = 'We Have Confirmed Your Topup';
                                    $saldo = $this->wlt->getsaldo($id_user);
                            
                                    $this->wlt->editsaldotopup($id_user, $amount, $saldo);
                                    $this->wlt->editstatuswithdrawbyid($id);
                                    $this->wlt->send_notif($title, $message, $topic);
                            
                            
                                    //Your code here
                                    echo "SUCCESS"; exit;// Please response with success
                                    
                    }
                    
                    echo "SUCCESS"; exit;
                                    
                                
                }
                else
                {
            
                     echo "BAD SIGNATURE"; exit;
                } 
                
            }
            else
            {
                echo "BAD PARAMETER"; exit;

            }
            
			
			

    }




}
