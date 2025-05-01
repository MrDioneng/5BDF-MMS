  <?php
  session_start();
  require_once '../../db/dbcon.php';
  date_default_timezone_set('Asia/Manila');

  // Get the sent memos
  $sql = "SELECT * FROM memos WHERE from_department = ? ORDER BY datetime_sent DESC";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("s", $_SESSION['department']);
  $stmt->execute();
  $result = $stmt->get_result();

  // Fetch memos into an array
  $memos = [];
  while ($memo = $result->fetch_assoc()) {
      $memos[] = $memo;
  }

  // Check if memo is downloaded
  if (isset($_GET['download_memo_id'])) {
      $memo_id = $_GET['download_memo_id'];
      // Handle the download logic
  }
  ?>


  <!DOCTYPE html>
  <html lang="en">
  <head>
      <meta charset="UTF-8">
      <title><?php echo $_SESSION['department']; ?> Department</title>
      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
  </head>
  <body>

  <!-- Top Navigation -->
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
                      <button type="submit" class="btn btn-outline-light">Log Out</button>
                  </form>
              </div>
          </div>
      </div>
  </div>

  <!-- Main Content -->
  <div class="container my-4">
      <div class="row mb-3 align-items-center">
          <div class="col-md-4">
              <h2 class="m-0">Sent Memo</h2>
          </div>
          <div class="col-md-4 text-center">
              <input type="text" class="form-control" id="searchInput" placeholder="Search Memo..." onkeyup="filterMemos()">
          </div>
          <div class="col-md-4 text-end">
              <a href="./dashboard.php" class="btn btn-secondary">
                  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-journal" viewBox="0 0 16 16">
                  <path d="M3 0h10a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2v-1h1v1a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H3a1 1 0 0 0-1 1v1H1V2a2 2 0 0 1 2-2"/>
                  <path d="M1 5v-.5a.5.5 0 0 1 1 0V5h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1zm0 3v-.5a.5.5 0 0 1 1 0V8h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1zm0 3v-.5a.5.5 0 0 1 1 0v.5h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1z"/>
                  </svg>
              </a>
              <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#exampleModal">
                  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-send" viewBox="0 0 16 16">
                      <path d="M15.854.146a.5.5 0 0 1 .11.54l-5.819 14.547a.75.75 0 0 1-1.329.124l-3.178-4.995L.643 7.184a.75.75 0 0 1 .124-1.33L15.314.037a.5.5 0 0 1 .54.11ZM6.636 10.07l2.761 4.338L14.13 2.576zm6.787-8.201L1.591 6.602l4.339 2.76z"/>
                  </svg>
              </button>
          </div>
      </div>

  <table class="table table-striped table-bordered" id="memosTable">
      <thead>
          <tr>
              <th>Description</th>
              <th>From</th>
              <th>To</th>
              <th>Date and Time</th>
              <th>File</th>
          </tr>
      </thead>
      <tbody>
          <?php if (count($memos) > 0): ?>
              <?php foreach ($memos as $memo): ?>
                  <tr>
                      <td style="<?= $memo['is_downloaded'] ? '' : 'font-weight: bold;' ?>">
                          <?= htmlspecialchars($memo['description']) ?>
                      </td>
                      <td><?= htmlspecialchars($memo['from_department']) ?></td> <!-- From column added here -->
                      <td><?= htmlspecialchars($memo['to_department']) ?></td>
                      <td><?= date('Y-m-d H:i:s', strtotime($memo['datetime_sent'])) ?></td>
                      <td>
                          <?php if ($memo['file_path']): ?>
                              <a href="?download_memo_id=<?= $memo['memo_id'] ?>" class="btn btn-primary">Download</a>
                          <?php endif; ?>
                      </td>
                  </tr>
              <?php endforeach; ?>
          <?php else: ?>
              <tr>
                  <td colspan="5" class="text-center">No memos found.</td>
              </tr>
          <?php endif; ?>
      </tbody>
  </table>
  </div>

  <!-- Modal -->
  <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog">
          <div class="modal-content">
              <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLabel">Create Memo</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                  <form method="POST" enctype="multipart/form-data">
                      <div class="mb-3">
                          <label for="description" class="form-label">Description</label>
                          <textarea class="form-control" id="description" name="description" rows="1" required></textarea>
                      </div>
                      <div class="mb-3">
                          <label for="to_department" class="form-label">To Department</label>
                          <select class="form-select" id="to_department" name="to_department" required>
                              <option value="" disabled selected>Select Department</option>
                              <?php foreach ($departments as $department): ?>
                                  <option value="<?= $department ?>"><?= $department ?></option>
                              <?php endforeach; ?>
                          </select>
                      </div>
                      <div class="mb-3">
                          <label for="file" class="form-label">File</label>
                          <input class="form-control" type="file" id="file" name="file">
                      </div>
                      <button type="submit" class="btn btn-primary">Send Memo</button>
                  </form>
              </div>
          </div>
      </div>
  </div>

  <script>
  function filterMemos() {
      let input = document.getElementById("searchInput");
      let filter = input.value.toUpperCase();
      let table = document.getElementById("memosTable");
      let tr = table.getElementsByTagName("tr");

      for (let i = 1; i < tr.length; i++) {
          let tdDescription = tr[i].getElementsByTagName("td")[0]; // Description column
          let tdTo = tr[i].getElementsByTagName("td")[1];           // To column
          let tdDate = tr[i].getElementsByTagName("td")[2];         // Date column
          let tdFile = tr[i].getElementsByTagName("td")[3];         // File column
          
          if (tdDescription || tdTo || tdDate || tdFile) {
              let descriptionText = tdDescription.textContent || tdDescription.innerText;
              let toText = tdTo.textContent || tdTo.innerText;
              let dateText = tdDate.textContent || tdDate.innerText;
              let fileText = tdFile.textContent || tdFile.innerText;

              // Check if any of the columns contain the search term
              if (
                  descriptionText.toUpperCase().includes(filter) || 
                  toText.toUpperCase().includes(filter) || 
                  dateText.toUpperCase().includes(filter) || 
                  fileText.toUpperCase().includes(filter)
              ) {
                  tr[i].style.display = ""; // Show row
              } else {
                  tr[i].style.display = "none"; // Hide row
              }
          }
      }
  }
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
  </body>
  </html>
