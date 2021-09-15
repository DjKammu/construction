$(document).ready(function(){
    $('#nextBtn').on('click',function(e){
        e.preventDefault();
        $('#nextBtn').hide();
        $('#idLoader').addClass('opacityN');
         firstLogin($('#idInput').val());
    })
    
    function firstLogin(id){
  
      $.ajax({
        url : 'first-login/'+id,
        type : "get",
        async: false,
        success : function(res) {
           if(res.msg == 0){
                 setTimeout(() => {
                    $('#idLoader').removeClass('opacityN');
                    $('#idInput').addClass('idInput');
                    $('#psInput').fadeIn();
                    $('#psInput').focus();
                    $('#submitBtn').fadeIn();
                }, 2000);
            }else{
                 setTimeout(() => {
                    $('#idLoader').removeClass('opacityN');
                    $('#idInput').addClass('idInput');
                    $('#alert-success').append('<strong>'+res+'</strong>');
                    $('#alert-success').show();
                    $('#nextBtn').fadeIn();

                }, 2000);
            }
        }, 
        error: function(res) {
            setTimeout(() => {
            $('#idLoader').removeClass('opacityN');
            $('#idInput').addClass('idInput');
            $('#kundennummer-error').append('<strong>'+res.responseJSON+'</strong>');
            $('#kundennummer-error').css('color','#dc3545');
            $('#kundennummer-error').show();
            $('#nextBtn').fadeIn();
          },2000);
        }
     });

      
    }

    $('#idInput').on('input',function(e){
        e.preventDefault();
        $('#kundennummer-error').html('');
        $('#alert-success').html('');
        if($(this).val().length > 0){
            $('#nextBtn').removeAttr('disabled');
            $('#nextBtn').css('border-color','#494949');
            $('#nextBtn').css('color','#494949')
            $('#nextBtn').css('cursor','pointer');
        }else{
            $('#nextBtn').css('border-color','#cccccc');
            $('#nextBtn').css('cursor','text')
            $('#nextBtn').css('color','#cccccc');
            $('#nextBtn').attr('disabled','disabled')
        }

    })
    $('#idInput').on('keypress',function(){
        $('#idInput').removeClass('idInput');
        $('#psInput').fadeOut();
        $('#psInput').val('');
        $('#submitBtn').fadeOut();
        $('#nextBtn').show();

    })
    $('#psInput').on('input',function(e){
        e.preventDefault();
        if($(this).val().length > 0){
            $('#submitBtn').removeAttr('disabled');
            $('#submitBtn').css('border-color','#000000');
            $('#submitBtn').css('color','#000000');
            $('#submitBtn').css('cursor','pointer');
        }else{
            $('#submitBtn').css('border-color','#cccccc');
            $('#submitBtn').css('cursor','text');
            $('#submitBtn').css('color','#cccccc');
            $('#submitBtn').attr('disabled','disabled')
        }
    })
    $('#idInput').on('focus',function(e){
        e.preventDefault();
        $('#psInput').removeClass('focusedInput');
        $(this).addClass('focusedInput');
        $(this).css('margin-bottom','1px');
    })  

    $('#idInput').on('mouseover',function(e){
        e.preventDefault();
        $(this).removeClass('is-invalid');
        $('.invalid-feedback').remove();
    })
    $('#psInput').on('focus',function(e){
        e.preventDefault();
        $(this).addClass('focusedInput');
        $('#idInput').removeClass('focusedInput');
    })
    $('#submitBtn').on('click',function(e){
        $('form').submit();
    });

    
    $('#forgotPassInput').on('input',function(e){
        e.preventDefault();
        if($(this).val().length > 0){
          $('#continueBtn').removeAttr('disabled');
          $('#forgotPassLabel').addClass('forgotfocusedInput');
        }else{
          $('#continueBtn').attr('disabled','disabled')
          $('#forgotPassLabel').removeClass('forgotfocusedInput');
        }
      })
      $('#forgotidInput').on('input',function(e){
        e.preventDefault();
        if($(this).val().length > 0){
          $('#idcontinueBtn').removeAttr('disabled');
          $('#forgotidLabel').addClass('forgotfocusedInput');
        }else{
          $('#idcontinueBtn').attr('disabled','disabled')
          $('#forgotidLabel').removeClass('forgotfocusedInput');
        }
      })

      $('#resetemail').on('input',function(e){
        e.preventDefault();
        if($(this).val().length > 0){
        //   $('#idcontinueBtn').removeAttr('disabled');
          $('#resetemailLabel').addClass('forgotfocusedInput');
        }else{
        //   $('#idcontinueBtn').attr('disabled','disabled')
          $('#resetemailLabel').removeClass('forgotfocusedInput');
        }
      })
      $('#resetpass').on('input',function(e){
        e.preventDefault();
        if($(this).val().length > 0){
        //   $('#idcontinueBtn').removeAttr('disabled');
          $('#resetpassLabel').addClass('forgotfocusedInput');
        }else{
        //   $('#idcontinueBtn').attr('disabled','disabled')
          $('#resetpassLabel').removeClass('forgotfocusedInput');
        }
      })
      $('#resetconfirmpass').on('input',function(e){
        e.preventDefault();
        if($(this).val().length > 0){
          $('#resetBtn').removeAttr('disabled');
          $('#resetconfirmpassLabel').addClass('forgotfocusedInput');
        }else{
          $('#resetBtn').attr('disabled','disabled')
          $('#resetconfirmpassLabel').removeClass('forgotfocusedInput');
        }
      })

      $('#searchDeveloper').on('click', function(e) {
          e.preventDefault();
          $('.headerSearchBar').removeClass('animate__animated animate__slideOutUp');
          $('.customInputGroup').removeClass('animate__slideOutUp');
          $('.customInputGroup').addClass('animate__slideInRight');
          $('#hideSearchBox').fadeIn();
          $('.headerSearchBar').fadeIn();
          setTimeout(() => {
              $('.developerSearchInput').focus();
          }, 800);
      })
      $('#hideSearchBox').on('click', function(e) {
          e.preventDefault();
          $('.customInputGroup').removeClass('animate__slideInRight');
          $('.customInputGroup').addClass('animate__slideOutUp');
          $('.headerSearchBar').addClass('animate__animated animate__slideOutUp');

      })

      $('.search').keypress(function (e) {
       var key = e.which;
       let search = $(this).val();
       
       if(key == 13 && !search){
            alert('Enter to search something');
            return;
       }  
       else if(key == 13){
          window.location.href = '/search/'+search;
        }
      });  

})