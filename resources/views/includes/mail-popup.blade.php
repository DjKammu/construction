

<div id="myModal" class="modal hide" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
    <div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">Send Mail</h3>
    </div>
    <div class="modal-body">
     
          <div class="form-group">
            <label for="recipient-name" class="col-form-label">Recipient:</label>
            <input type="email" class="form-control" id="recipient">
          </div>
          <div class="form-group">
            <label for="recipient-name" class="col-form-label">Subject:</label>
            <input type="text" class="form-control" id="subject">
          </div>
          <div class="form-group">
            <label for="message-text" class="col-form-label">Message:</label>
            <textarea class="form-control" id="message"></textarea>
          </div>
    
    </div>
    <div class="modal-footer">
        <button class="btn btn-close" data-dismiss="modal" aria-hidden="true">Close</button>
        <button class="btn btn-primary" onclick="sendMail()">Send</button>
    </div>
    </div>
    </div>

</div>


<script type="text/javascript">

  $(document).ready(function(){
      $(".btn-close").click(function(){  
            $("#myModal").modal('hide');
        });
  });

   function sendEmailPopup(){   
      $("#myModal").modal('show');
   }

   function sendMail(){
   
    var recipient = $('#recipient').val();
    var subject = $('#subject').val();
    var message = $('#message').val();
    var file = $('#file').val();

    const validateEmail = (email) => {
	  return String(email)
	    .toLowerCase()
	    .match(
	      /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
	    );
	};


    if(!recipient){
    	alert('Recipient cant be blank')
    	return
    }else if(!validateEmail(recipient)) {
        alert('Recipient is invalid')
    	return
  
    }else if(!subject){
    	alert('Subject cant be blank')
    	return
    } else if(!message){
    	alert('Message cant be blank')
    	return
    }
    
    let _token   =   "{{ csrf_token() }}";

   $.ajax({
        url: "{{ route('send.mail.pdf')}}",
        type:"POST",
        data:{
          recipient:recipient,
          subject:subject,
          message:message,
          file:file,
          _token: _token
        },
        success:function(response){
           alert(response.message); 
           //location.reload();
        },
        error: function(error) {
          alert(error);
        }
       });

   }

</script>