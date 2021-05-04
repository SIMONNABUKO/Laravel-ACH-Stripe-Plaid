
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="_token" content="{{ csrf_token() }}">
    <title>Document</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{asset('css/style.css')}}">
    <style></style>
</head>
<body>
    <div id="main">
        <div id="checkout">
            <div id="payment-form">
                <h1>Pay with ACH</h1>
                <input class="form-control" type="number" id="amount" placeholder="Enter Amount">
                <button id="link-button">Pay Now</button><br><br>
                <div class="d-none" id="success">
                  <div class="alert alert-success alert-dismissible fade show " role="alert">
                    <strong>Congratulations! Payment successful.</strong>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                </div>
                
            </div>
        </div>
    </div>

    <script src="https://cdn.plaid.com/link/v2/stable/link-initialize.js"></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.min.js"></script>

<script type="text/javascript">
let linkToken='sandbox-hjiio'
var amount = document.getElementById('amount');

window.onload = function() {
  let linkToken='sandbox-hjiio'
  if(amount.value  ==''){
    document.getElementById('link-button').disabled =true;
  }
  axios.get("http://stripeach.surviivorbet.com/link_token",)
    .then((response)=>{
        console.log(response);
        linkToken = response.data;
        kickOff(linkToken);
    })
    .catch(err=>console.log(err))
     
};

amount.addEventListener('change',function(){
  if(amount.value !=''){
    if(localStorage.getItem('amount')){
      localStorage.removeItem('amount');
      localStorage.setItem('amount', amount.value)
    }else{
    localStorage.setItem('amount', amount.value)  
    }
    document.getElementById('link-button').removeAttribute('disabled');
  }
})

function kickOff(linkToken) {
 
  const configs = {
    // Pass the link_token generated in step 2.
    token: linkToken,
    onLoad: function() {
      // The Link module finished loading.
    },
    onSuccess: function(public_token, metadata) {
      // The onSuccess function is called when the user has
      // successfully authenticated and selected an account to
      // use.
      //
      // When called, you will send the public_token
      // and the selected account ID, metadata.accounts,
      // to your backend app server.
      //
      getStripeToken(public_token,metadata.accounts[0].id)

      switch (metadata.accounts.length) {
        case 0:
          // Select Account is disabled: https://dashboard.plaid.com/link/account-select
          break;
        case 1:
          console.log('Customer-selected account ID: ' + metadata.accounts[0].id);
         
          break;
        default:
          // Multiple Accounts is enabled: https://dashboard.plaid.com/link/account-select
      }
    },
    onExit: async function(err, metadata) {
      // The user exited the Link flow.
      if (err != null) {
          // The user encountered a Plaid API error
          // prior to exiting.
      }
      // metadata contains information about the institution
      // that the user selected and the most recent
      // API request IDs.
      // Storing this information can be helpful for support.
    },
  };

  var linkHandler = Plaid.create(configs);

  document.getElementById('link-button').onclick = function() {
    linkHandler.open();
  };

  function getStripeToken(token, id) { 
    var amount = localStorage.getItem('amount');
    axios.post("http://stripeach.surviivorbet.com/exchange-tokens", {public_token: token, client_id: id, amount:amount})
    .then((response)=>{
      let div = document.getElementById('success');
        div.classList.remove('d-none');
        
    })
    .catch(err=>console.log(err))
 
   }
   
};
</script>
</body>
</html>

