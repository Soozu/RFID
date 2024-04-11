document.addEventListener('DOMContentLoaded', function() {
    // Fetch the student enrolled total when the document is fully loaded
    fetchStudentEnrolledTotal();
  });
  
  function fetchStudentEnrolledTotal() {
    fetch('get_student_total.php') // Your PHP endpoint for fetching the data
      .then(response => response.json())
      .then(data => {
        document.getElementById('student-enrolled-total').textContent = data.total_students || 'Unavailable';
      })
      .catch(error => {
        console.error('Error fetching student enrolled total:', error);
        document.getElementById('student-enrolled-total').textContent = 'Error';
      });
  }

  function fetchStudentLogsTotal() {
    fetch('get_student_logs_total.php') // Point this to your PHP script
      .then(response => response.json())
      .then(data => {
        document.getElementById('student-logs-total').textContent = data.total_logs || 'Unavailable';
      })
      .catch(error => {
        console.error('Error fetching student logs total:', error);
        document.getElementById('student-logs-total').textContent = 'Error';
      });
  }
  
  // Call this function alongside your existing ones
  document.addEventListener('DOMContentLoaded', function() {
    fetchStudentEnrolledTotal(); // Existing call
    fetchStudentLogsTotal();     // New call for logs total
  });
  

  