<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}
require_once '../../db/dbcon.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
  <title>Announcements</title>
</head>
<body>
  <div class="px-3 py-2 mb-5 text-bg-dark border-bottom">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-6 col-md-4">
                <span class="fs-3 text-white"><img src="../../img/5bdflogo.png" alt="5BDF Logo" style="width: 50px; height: auto;"> <?php echo $_SESSION['department']; ?></span>
            </div>
            <div class="col-6 col-md-4 d-flex justify-content-center">
                <ul class="nav col-12 col-lg-auto my-2 justify-content-center my-md-0 text-small">
                    <li class="nav-item mx-3 text-center">
                        <a href="memo.php" class="nav-link text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-file-earmark-fill" viewBox="0 0 16 16">
                                <path d="M4 0h5.293A1 1 0 0 1 10 .293L13.707 4a1 1 0 0 1 .293.707V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2m5.5 1.5v2a1 1 0 0 0 1 1h2z"/>
                            </svg>
                            <div>Memo</div>
                        </a>
                    </li>
                    <li class="nav-item mx-3 text-center">
                        <a href="announcements.php" class="nav-link text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-megaphone-fill" viewBox="0 0 16 16">
                                <path d="M13 2.5a1.5 1.5 0 0 1 3 0v11a1.5 1.5 0 0 1-3 0zm-1 .724c-2.067.95-4.539 1.481-7 1.656v6.237a25 25 0 0 1 1.088.085c2.053.204 4.038.668 5.912 1.56zm-8 7.841V4.934c-.68.027-1.399.043-2.008.053A2.02 2.02 0 0 0 0 7v2c0 1.106.896 1.996 1.994 2.009l.496.008a64 64 0 0 1 1.51.048m1.39 1.081q.428.032.85.078l.253 1.69a1 1 0 0 1-.983 1.187h-.548a1 1 0 0 1-.916-.599l-1.314-2.48a66 66 0 0 1 1.692.064q.491.026.966.06"/>
                            </svg>
                            <div>Announcements</div>
                        </a>
                    </li>
                    <li class="nav-item mx-3 text-center">
                        <a href="exams.php" class="nav-link text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                                <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/>
                            </svg>
                            <div>Exams</div>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="col-12 col-md-4 d-flex justify-content-end">
                <form action="../../index.php" method="POST">
                    <button class="btn btn-outline-danger ms-2" onclick="window.location.href='logout.php'">Logout</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="container my-4">
    <div class="row mb-3 align-items-center">
        <div class="col-md-4">
            <h2 class="m-0">Announcements</h2>
        </div>
        <div class="col-md-4 text-center">
            <input type="text" class="form-control" id="searchInput" placeholder="Search Announcement..." onkeyup="filterMemos()">
        </div>
    </div>

    <table class="table table-striped table-bordered" id="memosTable">
        <thead>
            <tr>
              <th>Date</th>
              <th>Title</th>
              <th>Action</th>
            </tr>
        </thead>
        <tbody>

        </tbody>
    </table>
</div>
<script>
  function filterAnnouncements() {
    var input, filter, table, tr, td, i, txtValue;
    input = document.getElementById('searchInput');
    filter = input.value.toLowerCase();
    table = document.querySelector('table');
    tr = table.getElementsByTagName('tr');

    for (i = 1; i < tr.length; i++) {
      td = tr[i].getElementsByTagName('td');
      if (td.length > 0) {
        txtValue = td[1].textContent || td[1].innerText;
        if (txtValue.toLowerCase().indexOf(filter) > -1) {
          tr[i].style.display = "";
        } else {
          tr[i].style.display = "none";
        }
      }
    }
  }
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
</body>
</html>