$(document).ready(function(){
  $(document).ready(function(){
    // Handle the button click event for fetching the user log
    $(document).on('click', '#user_log', function(){
      
      // Gather all filter parameters
      var date_sel_start = $('#date_sel_start').val();
      var date_sel_end = $('#date_sel_end').val();
      var time_sel = $(".time_sel:checked").val();
      var time_sel_start = $('#time_sel_start').val();
      var time_sel_end = $('#time_sel_end').val();
      var card_sel = $('#card_sel option:selected').val();
      var dev_uid = $('#dev_sel option:selected').val();
      
      // Make the AJAX call to the PHP script
      $.ajax({
        url: 'user_log_up.php',
        type: 'POST',
        data: {
          'log_date': 1,
          'date_sel_start': date_sel_start,
          'date_sel_end': date_sel_end,
          'time_sel': time_sel,
          'time_sel_start': time_sel_start,
          'time_sel_end': time_sel_end,
          'card_sel': card_sel,
          'dev_uid': dev_uid
        },
        beforeSend: function() {
          // Optional: Add a loading message or spinner
          $('#userslog').html('<div class="text-center">Loading...</div>');
        },
        success: function(response) {
          // Update the user log container with the response data
          $('#userslog').html(response);
  
          // Optional: Display a success message
          $('.up_info2').fadeIn(500).text("The Filter has been selected!").fadeOut(5000);
  
          // Hide the filter modal if you have one
          $('#Filter-export').modal('hide');
        },
        error: function(xhr, status, error) {
          // Handle any AJAX errors here
          console.error("An error occurred: " + error);
        }
      });
    });
  });
  
});