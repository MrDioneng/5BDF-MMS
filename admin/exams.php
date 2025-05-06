  <!DOCTYPE html>
  <html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <title>Exams</title>
    
  </head>
  <body>
  <header class="d-flex flex-wrap align-items-center justify-content-between py-2 px-4 border-bottom">
    <div class="d-flex align-items-center gap-2">
      <img src="../img/5bdflogo.png" alt="5BDF Logo" height="50" width="50">
      <h4 class="m-0">Admin</h4>
    </div>
    <nav>
      <ul class="nav">
          <li class="nav-item">
          <a href="#" class="nav-link d-flex flex-column align-items-center px-2">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-house mb-1" viewBox="0 0 16 16">
              <path d="M8.707 1.5a1 1 0 0 0-1.414 0L.646 8.146a.5.5 0 0 0 .708.708L2 8.207V13.5A1.5 1.5 0 0 0 3.5 15h9a1.5 1.5 0 0 0 1.5-1.5V8.207l.646.647a.5.5 0 0 0 .708-.708L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293zM13 7.207V13.5a.5.5 0 0 1-.5.5h-9a.5.5 0 0 1-.5-.5V7.207l5-5z"/>
              </svg>
              Home
          </a>
          </li>
          <li class="nav-item">
          <a href="./admin.php" class="nav-link d-flex flex-column align-items-center px-2">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-people mb-1" viewBox="0 0 16 16">
              <path d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1zm-7.978-1L7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72l-.008.002-.014.002zM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4m3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0M6.936 9.28a6 6 0 0 0-1.23-.247A7 7 0 0 0 5 9c-4 0-5 3-5 4q0 1 1 1h4.216A2.24 2.24 0 0 1 5 13c0-1.01.377-2.042 1.09-2.904.243-.294.526-.569.846-.816M4.92 10A5.5 5.5 0 0 0 4 13H1c0-.26.164-1.03.76-1.724.545-.636 1.492-1.256 3.16-1.275ZM1.5 5.5a3 3 0 1 1 6 0 3 3 0 0 1-6 0m3-2a2 2 0 1 0 0 4 2 2 0 0 0 0-4"/>
              </svg>
              Users
          </a>
          </li>
          <li class="nav-item">
          <a href="./departments.php" class="nav-link d-flex flex-column align-items-center px-2">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-building mb-1" viewBox="0 0 16 16">
              <path d="M4 2.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm3 0a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm3.5-.5a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zM4 5.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zM7.5 5a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm2.5.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zM4.5 8a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm2.5.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm3.5-.5a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5z"/>
              <path d="M2 1a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v14a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1zm11 0H3v14h3v-2.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 .5.5V15h3z"/>
              </svg>
              Departments
          </a>
          </li>
          <li class="nav-item">
          <a href="./memos.php" class="nav-link d-flex flex-column align-items-center px-2">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-journal mb-1" viewBox="0 0 16 16">
              <path d="M3 0h10a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2v-1h1v1a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H3a1 1 0 0 0-1 1v1H1V2a2 2 0 0 1 2-2"/>
              <path d="M1 5v-.5a.5.5 0 0 1 1 0V5h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1zm0 3v-.5a.5.5 0 0 1 1 0V8h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1zm0 3v-.5a.5.5 0 0 1 1 0v.5h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1z"/>
              </svg>
              Memos
          </a>
          </li>
          <li class="nav-item">
          <a href="./announcements.php" class="nav-link d-flex flex-column align-items-center px-2">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-megaphone mb-1" viewBox="0 0 16 16">
              <path d="M13 2.5a1.5 1.5 0 0 1 3 0v11a1.5 1.5 0 0 1-3 0v-.214c-2.162-1.241-4.49-1.843-6.912-2.083l.405 2.712A1 1 0 0 1 5.51 15.1h-.548a1 1 0 0 1-.916-.599l-1.85-3.49-.202-.003A2.014 2.014 0 0 1 0 9V7a2.02 2.02 0 0 1 1.992-2.013 75 75 0 0 0 2.483-.075c3.043-.154 6.148-.849 8.525-2.199zm1 0v11a.5.5 0 0 0 1 0v-11a.5.5 0 0 0-1 0m-1 1.35c-2.344 1.205-5.209 1.842-8 2.033v4.233q.27.015.537.036c2.568.189 5.093.744 7.463 1.993zm-9 6.215v-4.13a95 95 0 0 1-1.992.052A1.02 1.02 0 0 0 1 7v2c0 .55.448 1.002 1.006 1.009A61 61 0 0 1 4 10.065m-.657.975 1.609 3.037.01.024h.548l-.002-.014-.443-2.966a68 68 0 0 0-1.722-.082z"/>
              </svg>
              Announcements
          </a>
          </li>
          <li class="nav-item">
            <a href="./exams.php" class="nav-link d-flex flex-column align-items-center px-2">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square mb-1" viewBox="0 0 16 16">
                <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293z"/>
                <path d="M13.752 4.396l-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/>
              </svg>
              Exams
            </a>
          </li>
      </ul>
    </nav>

    <div>
      <button class="btn btn-outline-danger ms-2" onclick="window.location.href='logout.php'">Logout</button>
    </div>
  </header>
    
  <main class="d-flex">
    <!-- Sidebar -->
    <div class="flex-shrink-0 p-3 bg-dark text-white min-vh-100" style="width: 200px;">
      <a href="#" class="d-flex align-items-center pb-3 mb-3 text-white text-decoration-none border-bottom">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
          class="bi bi-pen" viewBox="0 0 16 16">
          <path
            d="m13.498.795.149-.149a1.207 1.207 0 1 1 1.707 1.708l-.149.148a1.5 1.5 0 0 1-.059 2.059L4.854 14.854a.5.5 0 0 1-.233.131l-4 1a.5.5 0 0 1-.606-.606l1-4a.5.5 0 0 1 .131-.232l9.642-9.642a.5.5 0 0 0-.642.056L6.854 4.854a.5.5 0 1 1-.708-.708L9.44.854A1.5 1.5 0 0 1 11.5.796a1.5 1.5 0 0 1 1.998-.001m-.644.766a.5.5 0 0 0-.707 0L1.95 11.756l-.764 3.057 3.057-.764L14.44 3.854a.5.5 0 0 0 0-.708z" />
        </svg>
        <span class="fs-5 fw-semibold ms-2">EXAM</span>
      </a>

      <ul class="list-unstyled ps-0">
        <!-- Menu Items -->
        <li class="mb-1">
          <a href="manage-exams.php" class="btn btn-toggle border-0 text-white d-flex justify-content-between align-items-center w-100">
            <span>Create Exam</span>
          </a>
        </li>

        <li class="mb-1">
          <a href="manage-exams.php" class="btn btn-toggle border-0 text-white d-flex justify-content-between align-items-center w-100">
            <span>Rank By Exam</span>
          </a>
        </li>

        <li class="mb-1">
          <a href="manage-exams.php" class="btn btn-toggle border-0 text-white d-flex justify-content-between align-items-center w-100">
            <span>Examinee Result</span>
          </a>
        </li>      
      </ul>
    </div>

    <!-- Main content -->
    <div class="flex-grow-1 p-3">
      <div class="card text-white mb-3" style="background: linear-gradient(to right, #3f51b5, #5a55ae); border-radius: 10px;">
        <div class="card-body d-flex justify-content-between align-items-center" style="height: 60px;">
          <h6 class="mb-0">Total Exams</h6>
          <h4 class="mb-0 fw-bold">4</h4>
        </div>
      </div>
    </div>

    <div class="flex-grow-1 p-3">
      <div class="card text-white mb-3" style="background: linear-gradient(to right, #3f51b5, #5a55ae); border-radius: 10px;">
        <div class="card-body d-flex justify-content-between align-items-center" style="height: 60px;">
          <h6 class="mb-0">Total Examinee</h6>
          <h4 class="mb-0 fw-bold">4</h4>
        </div>
      </div>
    </div>

    <div class="flex-grow-1 p-3">
      <div class="card text-white mb-3" style="background: linear-gradient(to right, #3f51b5, #5a55ae); border-radius: 10px;">
        <div class="card-body d-flex justify-content-between align-items-center" style="height: 60px;">
          <h6 class="mb-0">Highest Score By Exam</h6>
          <h4 class="mb-0 fw-bold"></h4>
        </div>
      </div>
    </div>
  </main>




  <script>
    document.querySelectorAll('[data-bs-toggle="collapse"]').forEach(button => {
      const icon = button.querySelector('i');
      const target = document.querySelector(button.getAttribute('data-bs-target'));

      target.addEventListener('show.bs.collapse', () => {
        icon.classList.remove('bi-chevron-right');
        icon.classList.add('bi-chevron-down');
      });

      target.addEventListener('hide.bs.collapse', () => {
        icon.classList.remove('bi-chevron-down');
        icon.classList.add('bi-chevron-right');
      });
    });
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
  </body>
  </html>