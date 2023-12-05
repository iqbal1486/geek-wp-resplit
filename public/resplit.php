<?php
function geek_init_resplit_callback(){
    $user_ip    = geek_client_ip_address();
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    
    $log = "User IP: ".$user_ip." and User Agent: ".$user_agent."\n";
    $log .= "Remote Address: ".$_SERVER['REMOTE_ADDR']." Real Address: ".$_SERVER['HTTP_X_REAL_IP']."\n";

    // Create a consistent hash using the IP and User-Agent
    // Manage cookie
    $cookie_name = "resplit_user";
    if(!isset($_COOKIE[$cookie_name])) {
        $hash           = md5($user_ip . $user_agent);
        $cookie_value   = $hash;
        setcookie($cookie_name, $cookie_value, time() + (86400 * 30 * 10), "/"); // 86400 = 10 day
    } else {
        $hash = $_COOKIE[$cookie_name];
    }

    $log .= "Hash Value: ".$hash."\n";

    $variant    = (hexdec($hash[0]) < 8) ? 'A' : 'B';
    $base_urls = $destination_urls = array();
    $rows = get_field('split_test_url_mapping', 'option');
    
    
    if( $rows ) {
        foreach( $rows as $key => $row ) {
            foreach ($row as $inner_key => $value) {
                if($inner_key == 'base_url'){
                    $base_urls[] = $value;
                }
                if($inner_key == 'destination_url'){
                    $destination_urls[] = $value;
                }
            }
        }
    }
    
    $log .= "Base URLs: ".print_r($base_urls, true)."\n";
    $log .= "Destination URLs: ".print_r($destination_urls, true)."\n";

    // $base_urls = [
    //             'https://circle.lifeandcanvas.com/affiliate-area/',
    //             'https://circle.lifeandcanvas.com/i2i-masterclass/',
    //             'https://circle.lifeandcanvas.com/friendship-academy-i2i/'
    //         ];

    // $destination_urls = [
    //             'https://circle.lifeandcanvas.com/friendship-academy-stoc/',
    //             'https://circle.lifeandcanvas.com/i2i-masterclass-b/',
    //             'https://circle.lifeandcanvas.com/friendship-academy-ttp/',
    //         ];

    // https://socialself.com/i2i-masterclass-video/?first_name=David+Morin&email=daver.morin%2Boct13%40gmail.com&ck_subscriber_id=2364359966
    // https://circle.lifeandcanvas.com/friendship-academy-ttp/?first_name=David+Morin&email=daver.morin%2Boct13%40gmail.com&ck_subscriber_id=2364359966

    $query_string   = $_SERVER['QUERY_STRING'];
    $current_url    = home_url($_SERVER['REQUEST_URI']);
    $current_url    = strtok($current_url, '?');

    $log .= "Current URL ".$current_url."\n";
    $log .= "Query String ".$query_string."\n";
    $log .= "variant ".$variant."\n";
    
    geek_generate_log($log);

    //DEBUG MODE
    if(isset($_GET['debug_split']) && $_GET['debug_split'] == 'yes'){
        
        echo "<pre>";
        echo $user_ip;
        echo "</pre>";
        
        // echo "<pre>";
        // print_r( $_SERVER );
        // echo "</pre>";

        echo "<pre>";
        echo $hash;
        echo "</pre>";

        echo "<pre>";
        print_r($variant);
        echo "</pre>";

        echo "<pre>";
        print_r($base_urls);
        echo "</pre>";

        echo "<pre>";
        print_r($destination_urls);
        echo "</pre>";

        echo "<pre>";
        print_r($current_url);
        echo "</pre>";
    }

    if($variant == 'B'){
        //For Variant B
        $urls           = $base_urls;
        $redirect_urls  = $destination_urls;
    }else{
        //For Variant A
        $urls           = $destination_urls;
        $redirect_urls  = $base_urls;
    }

    if( $urls ){
        foreach ($urls as $key => $url) {
            if ($current_url == $url ) {

                //geek_generate_log($log);

                $redirect_to = $redirect_urls[$key];

                if($query_string){
                    $redirect_to = $redirect_to.'?'.$query_string;
                }
                header("Location: $redirect_to");
                exit;
            }
        }    
    }
}
add_action('init', 'geek_init_resplit_callback');
?>