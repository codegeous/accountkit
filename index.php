<html>
    <head>
        <script src="jquery-3.0.0.min.js"></script>
        <script src="https://sdk.accountkit.com/en_US/sdk.js"></script>
        <style>
            body{
                font-family: tahoma;
            }
            .message {
                background-color: #222;
                border: 1px solid #dcdcdc;
                color: #fff;
                font-family: tahoma;
                margin-top: 84px;
                min-height: 250px;
                padding: 2px 45px;
                text-align: left;
                width: 50%;
                word-wrap: break-word;
            }
        </style>
    </head>

    <body>
        <div>
            <center>
                <input value="+1" id="country_code" />
                <input placeholder="phone number" id="phone_number"/>
                <button onclick="smsLogin();">Login via SMS</button>
                <div>OR</div>
                <input placeholder="email" id="email"/>
                <button onclick="emailLogin();">Login via Email</button>
                
                <div class="message">
                    <p><center><b>Message Board</b></center></p>
                </div>
            </center>
        </div>
        
        
        <script>
          //https://developers.facebook.com/docs/accountkit/webjs
          $(".message").append("<p>initialized Account Kit.</p>");
          
          // initialize Account Kit with CSRF protection
          AccountKit_OnInteractive = function(){
            AccountKit.init(
              {
                appId:"YOUR_FACEBOOK_APP_ID", 
                state:"CSRF_TOKEN", 
                version:"v1.0",
                fbAppEventsEnabled:true
              }
            );
          };

            
          // login callback
          function loginCallback(response) {
            if (response.status === "PARTIALLY_AUTHENTICATED") {
              var code = response.code;
              var csrf = response.state;
                $(".message").append("<p>Received auth token from facebook -  "+ code +".</p>");
                $(".message").append("<p>Triggering AJAX for server-side validation.</p>");
                
                $.post("verify.php", { code : code, csrf : csrf }, function(result){
                    $(".message").append( "<p>Server response : " + result + "</p>" );
                });
                
            }
            else if (response.status === "NOT_AUTHENTICATED") {
              // handle authentication failure
                $(".message").append("<p>( Error ) NOT_AUTHENTICATED status received from facebook, something went wrong.</p>");
            }
            else if (response.status === "BAD_PARAMS") {
              // handle bad parameters
                $(".message").append("<p>( Error ) BAD_PARAMS status received from facebook, something went wrong.</p>");
            }
          }
            
            
          // phone form submission handler
          function smsLogin() {
            var countryCode = document.getElementById("country_code").value;
            var phoneNumber = document.getElementById("phone_number").value;
            $(".message").append("<p>Triggering phone validation.</p>");
            AccountKit.login(
              'PHONE', 
              {countryCode: countryCode, phoneNumber: phoneNumber}, // will use default values if not specified
              loginCallback
            );
          }


          // email form submission handler
          function emailLogin() {
            var emailAddress = document.getElementById("email").value;
            AccountKit.login(
              'EMAIL',
              {emailAddress: emailAddress},
              loginCallback
            );
          }
        </script>
        
    </body>
</html>