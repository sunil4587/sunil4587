<?php


if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// $message = bbCustomMessage()->getMessage('earnTicketPoint');

// if($message !== false){

// }

class bbCustomMessage{

  /**
  * The one, true instance of this object.
  *
  * @static
  * @access private
  * @var null|object
  */
  private static $instance = null;

  private $message = [
    'loggedIn' => [

      // Profile page ticket section
      'ticketWinning' => '<h3>Congratulations!</h3>you are the winner of <b>{{TITLE}}</b> with ticket code- <b>{{CODE}}</b>.<br> <a href="{{CONTACT_LINK}}">Contact us</a> to claim your reward.',
      'ticketExpired' => 'Your ticket- <b>{{CODE}}</b> for <b>{{TITLE}}</b> has been already expired on <b>{{END_DATE}}</b> . <a href="{{REWARD_LINK}}">Click here</a> to buy one.',
      'ticketActive' => 'You have already Enrolled with ticket code - <b>{{CODE}}</b> for <b>{{TITLE}}</b><br>Result will be announced on <b>{{END_DATE}}</b>',
      'ticketRedeemed' => 'You have already <strong>redeemed</strong> your reward for <b>{{TITLE}}</b> with ticket code - <b>{{CODE}}</b>',


      //
      'pointThankYouConfirmation' => 'Your purchased item\'s codes are sent to your email or you can review your items on <a href="{{ACCOUNT_LINK}}">My account</a> page or <strong>below</strong>.',

      'ticketThankYouConfirmation' => 'Free raflle ticket will be given to you once order is completed.<br> It will appear in the confirmation email or you can check it on 
       <a href="{{ACCOUNT_LINK}}my-tickets">My Tickets</a> page.',

      //  #Shwoing message, when adding normal products based when cart have point based products
       'errorMessage' => [
        "Normal product were removed from the cart, because you have add a point specific product.",
        "Point specific product were removed from the cart, because you have add a normal product.",          
      ],

       #Shwoing message, when adding point based when cart have normal products
      'confirmationMessage' => [
        'Adding a point based product will remove all the existing product in the cart, are you sure you want to proceed?',
        'Adding a normal product will remove all the existing point based product in the cart, are you sure you want to proceed?',
      ]
    ],
    'nonLoggedIn' => [
      'loginMessageToAddPintBasedItem' => '<a href="{{LOGIN_LINK}}">Login </a> to your account to purchase point based products.',
      'loginToredeemTicket' => '<a href="{{LOGIN_LINK}}"> Login </a>to your account to reedem ticket',

    ]
  ];

  public function getBasicMessage($key){
    $loggedInMessage = $this->message['loggedIn'];
    $nonLoggedInMessage = $this->message['nonLoggedIn'];

    if( !isset($loggedInMessage[$key]) && !isset($nonLoggedInMessage[$key])  ){
      return false;
    }
 
    # Nonlogged is consider as default
    if( !isset($loggedInMessage[$key])){
      return $nonLoggedInMessage[$key];
    }

    # Logged
    $user = wp_get_current_user();
    if(empty($user)){
      return isset($nonLoggedInMessage[$key]) ? $nonLoggedInMessage[$key] : false;
    }
    
    return $loggedInMessage[$key];
  }

  public function getMessage($key, $mapping = []){
    $message = $this->getBasicMessage($key);
    if($message === false){
      return $message;
    }

    foreach( $mapping as $key => $value ){
      $message = str_replace("{{{$key}}}", $value, $message);
    }
    return $message;
  }

  public function __construct(){

    #showing for getting ticket on thankyou pgae  #woocommerce_order_details_after_order_table
    add_action( 'woocommerce_before_thankyou', [$this, 'showCouponMesssageOnthankyouPage'], 10, 4); 

    #checking page id after redirect
    add_action ('template_redirect', [$this, 'AddinghookdoubleXPreventAlert']);

    #
    #Adding alert when Double XP event is on.
    add_action('wp_footer', [$this ,'doubleXPeventAlert'],10);

  }

  public function showCouponMesssageOnthankyouPage($order_get_id){
    ob_start();
    $ordeID = wc_get_order_id_by_order_key( $_GET['key'] );
    $url = get_permalink( get_option( 'woocommerce_myaccount_page_id' )); 
    if(empty($ordeID)){
      return;
    }

    if( myCredCheckoutMofication()->isPointBasedOrder($ordeID)){ 


      $confirmationMessage = bbCustomMessage()->getMessage( "pointThankYouConfirmation", [
        'ACCOUNT_LINK' => $url
      ]); 

      if( $confirmationMessage !== false){
        ?>
        <div class="ids-ticket-massage">
          <span><?php echo $confirmationMessage; ?></span>
        </div>
        <?php
      }
    ?>

    <?php }
    if( !myCredCheckoutMofication()->isPointBasedOrder($ordeID) && is_user_logged_in()){ 
      $confirmationMessage = bbCustomMessage()->getMessage( "ticketThankYouConfirmation", [
        'ACCOUNT_LINK' => $url
      ]); 
      ?>
    <div class="ids-ticket-massage">
      <span><?php echo  $confirmationMessage ?></span>
    </div>
    <?php
    } 
    echo ob_get_clean();
  }
  
  public function AddinghookdoubleXPreventAlert(){
    if(is_cart() || is_checkout()){
     return false;
    }
    return true;
  } 

  public function doubleXPeventAlert(){
    if ($this->AddinghookdoubleXPreventAlert() == false){
      return;
    }
    if(empty(woodmart_get_opt( 'weekend_double_xp' )) ){
     return;
    }
    ob_start();?>
    <div class="elementor-section-wrap">
      <section class="wd-negative-gap elementor-section elementor-top-section elementor-element elementor-element-a14fa9c elementor-section-boxed elementor-section-height-default elementor-section-height-default wd-section-disabled" data-id="a14fa9c" data-element_type="section">
        <div class="elementor-container elementor-column-gap-default">
            <div class="elementor-row">
              <div class="elementor-column elementor-col-100 elementor-top-column elementor-element elementor-element-e746ff9" data-id="e746ff9" data-element_type="column">
                  <div class="elementor-column-wrap elementor-element-populated">
                    <div class="elementor-widget-wrap">
                        <div class="elementor-element elementor-element-5e300e3 color-scheme-inherit text-left elementor-widget elementor-widget-text-editor" data-id="5e300e3" data-element_type="widget" data-widget_type="text-editor.default">
                          <div class="elementor-widget-container">
                              <div class="elementor-text-editor elementor-clearfix">
                                <div class="woocommerce-info ids-custom-doublxp-notice"><strong>Double XP</strong>Event is on. Get double points on each purchase.<a href="<?php echo get_permalink( woocommerce_get_page_id( 'shop' ) ) ?>">Purchase now </a></div>
                              </div>
                          </div>
                        </div>
                    </div>
                  </div>
              </div>
            </div>
        </div>
      </section>
    </div>
  <?php echo ob_get_clean();
  }


  /**
   *  Function Name : error_reporting
   *  Working       : This function is used for php error_reporting.
  */
  public function error_reporting(){
    if( $this->errors === true ){
      ini_set('display_errors', 1);
      ini_set('display_startup_errors', 1);
      error_reporting(E_ALL); 
    }    
  }


  /**
   *  Function Name : debug
   *  Working       : It is used to debug the code, and printing the array passed to it
   *  Params        : Array needed to be print. 
  */
  public function debug($var){
    echo "<pre>";
      print_r($var);
    echo "</pre>";
  }

  /**
   *  Function Name : debugDump
   *  Working       : It is used to debug the code, and printing the array passed to it
   *  Params        : Array needed to be print. 
  */
  public function debugDump($var){
    echo "<pre>";
      var_dump($var);
    echo "</pre>";
  }




  /**
  * Get a unique instance of this object.
  *
  * @return object
  */
  public static function get_instance() {
    if ( null === self::$instance ) {
      self::$instance = new bbCustomMessage();
    }
    return self::$instance;
  }

}
