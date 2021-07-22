<?php
namespace AmiPOS\Library\Payments;

 /**
 * Liberia para la implementación de API Wompi Banco Agricola 
 * Esta libreria esta creada con PHP 7 o superior
 * @version 0.4
 *
 * @author Luis Angel Hernández
 * Email: lahernandezr@outlook.com
 * https://api.wompi.sv/index.html V1.0
 * 
 *
 */

class Wompi{

    public static $audience = "wompi_api";

     /**
     * @access private
     * @var array
     */
    private $access_token = [];  

     /**
     * @access private
     * @var array
     */
    private $type_data_return = "array"; // array o JSON


    /**
     * Establece el modo en que las funciones devolveran los resultados en String JSON o Array, por default siempre regresara en Array. 
     *
     * Url para extraer token: https://id.wompi.sv/connect/token
     * @access public
     * @param string $type  JSON o Array.
     */
    public function set_type_return($type){

        $type = ($type == "JSON") ? "JSON":"array";
        $this->type_data_return = $type;
    }

    /**
     * Convierte la informacion data a JSON o Array, por default siempre regresara en Array. 
     *
     * Url para extraer token: https://id.wompi.sv/connect/token
     * @access public
     * @param string $data datos a convertir en array o JSON.
     */
    public function convert_data($data){
        
        if($this->type_data_return == "JSON"){
            
            return json_encode(json_decode($data,true), JSON_PRETTY_PRINT);            
        }
        else{
            return json_decode($data,true);
        }
    }

    /**
     * La autenticación del API de Wompi se realiza utilizando OAuth 2.0 con Client Credential Flow. 
     *
     * Url para extraer token: https://id.wompi.sv/connect/token
     * @access public
     * @param string $app_id  El client id del aplicativo de wompi bajo el cual desea ejecutar la acción en el api
     * @param string $app_secret Es la llave secreta del aplicativo de wompi bajo el cual desea ejecutar la acción en el api
     * @return token
     */
    public function get_token($app_id, $app_secret){
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => "https://id.wompi.sv/connect/token",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS => "grant_type=client_credentials&client_id=".$app_id."&client_secret=".$app_secret."&audience=".Wompi::$audience,
          CURLOPT_HTTPHEADER => array(
            "content-type: application/x-www-form-urlencoded"
          ),
        ));
        
        $response = curl_exec($curl);
        $err = curl_error($curl);        
        curl_close($curl);        
        if ($err) {
            $this->access_token = [];
            return  $this->convert_data($err);
        } else {
            $this->access_token = json_decode($response,true);            
            return  $this->convert_data($response);        
        }
    }

    public function get_access_token(){
        return $this->access_token;
    }

    /**
     * Consulta los datos del aplicativo con el cual esta actualmente autenticado, incluyendo la información de el listado de cuotas validas y si aplica el pago con puntos
     *
     * Url: https://api.wompi.sv/Aplicativo
     * @access public
     * @param array  $tarjetaCreditoDebito Objeto tarjetaCreditoDebido
     * @param double $monto El monto que se desea cargar a la tarjeta de crédito en la transacción
     * @param string $emailCliente El e-mail del cliente que realiza la compra. Es de utilidad ya que allí es donde Wompi enviará una notificación de su compra al cliente su se activa dicha opción. 
     * @param string $nombreCliente El nombre del cliente que realiza la compra.
     * @return aplicativo
     */

    public function aplicativo(){
       
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => "https://api.wompi.sv/Aplicativo",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "GET",          
          CURLOPT_HTTPHEADER => array(
            "authorization: Bearer ".$this->access_token['access_token'],
            "content-type: application/json"                          
          ),    
        ));   
        $response = curl_exec($curl);
        $err = curl_error($curl);        
        if (!curl_errno($curl)) {
            $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $info = curl_getinfo($curl);            
          }        
        curl_close($curl);        
  

        if ($err) {                   
            return  $this->convert_data($err);
        } else {            
            return $this->convert_data($response);        
        }  
            
    }    


    /**
     * Consulta los datos de la cuenta con el cual esta actualmente autenticado, incluyendo la información de el listado de aplicativos
     *
     * Url: https://api.wompi.sv/Cuenta
     * @access public
     * @return cuenta
     */

    public function cuenta(){       
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => "https://api.wompi.sv/Cuenta",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "GET",          
          CURLOPT_HTTPHEADER => array(
            "authorization: Bearer ".$this->access_token['access_token'],
            "content-type: application/json"                          
          ),    
        ));   
        $response = curl_exec($curl);
        $err = curl_error($curl);        
        if (!curl_errno($curl)) {
            $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $info = curl_getinfo($curl);            
          }        
        curl_close($curl);        
        if ($err) {       
            return  $this->convert_data($err);
        } else {
            return $this->convert_data($response);        
        }  
            
    }   


    /**
     * Consulta una transacción creada previamente incluyendo e resultado de la misma
     *
     * Url: https://api.wompi.sv/TransaccionCompra/{id}
     * @access public
     * @return cuenta
     */

    public function transaccion_cuenta($id){

       
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => "https://api.wompi.sv/TransaccionCompra/$id",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "GET",          
          CURLOPT_HTTPHEADER => array(
            "authorization: Bearer ".$this->access_token['access_token'],
            "content-type: application/json"                          
          ),    
        ));   
        $response = curl_exec($curl);
        $err = curl_error($curl);        
        if (!curl_errno($curl)) {
            $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $info = curl_getinfo($curl);            
          }        
        curl_close($curl);        
  

        if ($err) {                   
            return  $this->convert_data($err);
        } else {            
            return $this->convert_data($response);       
        }  
            
    }     
    
    /**
     * Obtiene el comprobante de pago de la transacción
     *
     * Url: https://api.wompi.sv/TransaccionCompra/{idTransaccion}/comprobante
     * @access public
     * @return cuenta
     */

    public function transaccion_comprobante($id){

       
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => "https://api.wompi.sv/TransaccionCompra/$id/comprobante",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "GET",          
          CURLOPT_HTTPHEADER => array(
            "authorization: Bearer ".$this->access_token['access_token'],
            "content-type: application/json"                          
          ),    
        ));   
        $response = curl_exec($curl);
        $err = curl_error($curl);        
        if (!curl_errno($curl)) {
            $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $info = curl_getinfo($curl);            
          }        
        curl_close($curl);        
  

        if ($err) {                   
            return  $this->convert_data($err);
        } else {
            
            return  $this->convert_data($response);        
        }  
            
    }

    /**
     * Permite ejecutar una transacción de comprar enviando al API los datos del método de pago del cliente. 
     *
     * Url: https://api.wompi.sv/TransaccionCompra
     * @access public
     * @param array  $tarjetaCreditoDebito Objeto tarjetaCreditoDebido ver dococumentación API https://docs.wompi.sv/metodos-api/crear-transaccion-compra
     * @param double $monto El monto que se desea cargar a la tarjeta de crédito en la transacción
     * @param string $emailCliente El e-mail del cliente que realiza la compra. Es de utilidad ya que allí es donde Wompi enviará una notificación de su compra al cliente su se activa dicha opción. 
     * @param string $nombreCliente El nombre del cliente que realiza la compra.
     * @param string $formaPago Permite especifica la forma de pago que se desea usar "PagoNomal" para compra con tarjeta de crédito o débito. "Puntos" para pagos con puntos de Banco Agrícola. "Cuotas" para pago en cuotas con tarjetas de crédito de Banco Agrícola
     * @param array  $configuracion Objeto Configuración ver dococumentación API https://docs.wompi.sv/metodos-api/crear-transaccion-compra
     * @param array  $datosAdicionales Objeto Configuración ver dococumentación API https://docs.wompi.sv/metodos-api/crear-transaccion-compra
     * @param int $cantidadCuotas Requerido únicamente si se esteblacio "Cuotas" como forma de pago
     * @return trx
     */

     
    public function transaction($tarjetaCreditoDebito,$monto,$emailCliente,$nombreCliente,$formaPago="PagoNormal",$configuracion=[],$datosAdicionales=[],$cantidadCuotas=-1){
      
        $request = array( "tarjetaCreditoDebido"=>$tarjetaCreditoDebito,
                          "monto" => $monto,
                          "emailCliente"=> $emailCliente,
                          "nombreCliente"=> $nombreCliente);
        
        $request['formaPago']=$formaPago;
        if(count($configuracion)>0)
            $request['configuracion']=$configuracion;
        if(count($datosAdicionales)>0)
            $request['configuracion']=$datosAdicionales;    
        if($cantidadCuotas!=-1)
            $request['cantidadCuotas']=$cantidadCuotas;                                
        $data_string = json_encode($request);
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => "https://api.wompi.sv/TransaccionCompra",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS => $data_string,
          CURLOPT_HTTPHEADER => array(
            "authorization: Bearer ".$this->access_token['access_token'],
            "content-type: application/json"                          
          ),    
        ));   
        $response = curl_exec($curl);
        $err = curl_error($curl);        
        if (!curl_errno($curl)) {
            $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $info = curl_getinfo($curl);            
          }        
        curl_close($curl);        
        if ($err) {                  
            return  $this->convert_data($err);
        } else {
            return $this->convert_data($response);        
        }  
        
    }


}