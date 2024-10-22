<?php
session_start();
include('connection.php');

// Check if user is logged in, otherwise redirect to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch students from the database
$sql = "SELECT id, name, subject, marks FROM students";
$result = mysqli_query($conn, $sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        a {
            text-decoration: none;
            color: #000;
        }
        .site-header { 
            border-bottom: 1px solid #ccc;
            padding: .5em 1em;
            display: flex;
            justify-content: space-between;
        }

        .site-identity h1 {
            font-size: 1.5em;
            margin: .6em 0;
            display: inline-block;
        }

        .site-navigation ul, 
        .site-navigation li {
            margin: 0; 
            padding: 0;
        }

        .site-navigation li {
            display: inline-block;
            margin: 1.4em 1em 1em 1em;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            background-color: white;
        }

        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #4CAF50;
            color: white;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .edit, .delete, .save {
            border: none;
            padding: 8px 12px;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
            margin: 0 5px;
        }

        .edit {
            background-color: #ffc107;
            color: black;
            transition: background-color 0.3s;
        }

        .edit:hover {
            background-color: #e0a800;
        }

        .delete {
            background-color: #dc3545;
            color: white;
            transition: background-color 0.3s;
        }

        .delete:hover {
            background-color: #c82333;
        }

        .save {
            background-color: #28a745;
            color: white;
            transition: background-color 0.3s;
        }

        .save:hover {
            background-color: #218838;
        }

        @media (max-width: 600px) {
            th, td {
                padding: 10px;
                font-size: 12px;
            }
        }

        .initials-circle {
            display: inline-block;
            width: 40px;             
            height: 40px;             
            line-height: 40px;
            border-radius: 50%;
            background-color: #3498db; 
            color: white;  text-align: center;
            font-weight: bold;
            font-size: 16px;  
            margin-right: 10px; 
            font-family: Arial, sans-serif;    
        }
    </style>
</head>
<body>

<header class="site-header">
  <div class="site-identity">
    <h1><a href="index.php">Tailwebs</a></h1>
  </div>  
  <nav class="site-navigation">
    <ul class="nav">
      <li><a href="index.php">Home</a></li> 
      <li><a href="logout.php">Logout</a></li> 
    </ul>
  </nav>
</header>


<div class="text-end mt-5">
  <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#addStudentModal">
    Add New Student
  </button>
</div>

<!-- Modal Structure -->
<div class="modal fade" id="addStudentModal" tabindex="-1" aria-labelledby="addStudentModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addStudentModalLabel">Add New Student</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="addStudentForm">
          <div class="mb-3">
            <label for="studentName" class="form-label">Student Name</label>
            <input type="text" class="form-control" id="studentName" required autocomplete="off">
          </div>
          <div class="mb-3">
            <label for="subjectName" class="form-label">Subject</label>
            <input type="text" class="form-control" id="subjectName" required autocomplete="off">
          </div>
          <div class="mb-3">
            <label for="marks" class="form-label">Marks</label>
            <input type="number" class="form-control" id="marks" required autocomplete="off">
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="saveStudent">Save Student</button>
      </div>
    </div>
  </div>
</div>

<table>
    <thead>
        <tr>
            <th>Name</th>
            <th>Subject</th>
            <th>Marks</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody id="studentTable">
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <tr data-id="<?php echo $row['id']; ?>">
            <td class="name">
                <?php 
                    $words = explode(" ", $row['name']);
                    $initials = "";
                    foreach ($words as $word) {
                        $initials .= strtoupper($word[0]);
                    }
                    echo "<span class='initials-circle'>$initials</span>&nbsp; &nbsp;"; 
                    echo $row['name']; 
                ?>
            </td>
            <td class="subject"><?php echo $row['subject']; ?></td>
            <td class="marks"><?php echo $row['marks']; ?></td>
            <td>
                <button class="edit">Edit</button>
                <button class="delete" data-id="<?php echo $row['id']; ?>">Delete</button>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<script>
    // Event delegation for Edit and Save functionality
    document.addEventListener('click', function(e) {
        const target = e.target;

        // Inline editing functionality
        if (target.classList.contains('edit')) {
            const row = target.closest('tr');
            row.querySelectorAll('td').forEach(function(td) {
                if (td.classList.contains('name') || td.classList.contains('subject') || td.classList.contains('marks')) {
                    const currentValue = td.textContent.trim();
                    td.innerHTML = `<input type="text" class="form-control" value="${currentValue}">`;
                }
            });

            // Change the button text from Edit to Save
            target.textContent = 'Save';
            target.classList.replace('edit', 'save');
        }

        // Save edited data
        else if (target.classList.contains('save')) {
            const row = target.closest('tr');
            const id = row.dataset.id;
            const name = row.querySelector('.name input').value;
            const subject = row.querySelector('.subject input').value;
            const marks = row.querySelector('.marks input').value;

            if (name === '' || subject === '' || marks === '') {
                alert('Please fill all the fields');
                return;
            }

            const namePattern = /^[A-Za-z\s]+$/;
            if (!namePattern.test(name)) {
                alert('Student name should only contain letters and spaces.');
                return;
            }

            if (!namePattern.test(subject)) {
                alert('Subject name should only contain letters and spaces.');
                return;
            }

            const marksPattern = /^[0-9]+$/; 
            if (!marksPattern.test(marks)) {
                alert('Marks should only contain digits.');
                return;
            }

            // Send updated data via AJAX
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'update.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                if (xhr.status === 200) {
                    // Update the row with the new data (remove input fields and replace with text)
                    row.querySelector('.name').innerHTML = name;
                    row.querySelector('.subject').innerHTML = subject;
                    row.querySelector('.marks').innerHTML = marks;

                    // Change the button back to Edit
                    target.textContent = 'Edit';
                    target.classList.replace('save', 'edit');
                }
            };

            // Send the updated data via the AJAX request
            xhr.send(`id=${id}&name=${name}&subject=${subject}&marks=${marks}`);
        }
    });

    // Delete student
    document.querySelectorAll('.delete').forEach(function(deleteButton) {
        deleteButton.addEventListener('click', function() {
            const row = this.closest('tr');
            const id = this.dataset.id;

            // Send delete request via AJAX
            if (confirm('Are you sure you want to delete this student?')) {
                const xhr = new XMLHttpRequest();
                xhr.open('POST', 'delete.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        // Remove the row from the table
                        row.remove();
                    }
                };
                xhr.send(`id=${id}`);
            }
        });
    });

    document.getElementById('saveStudent').addEventListener('click', function() {
        const name = document.getElementById('studentName').value.trim();
        const subject = document.getElementById('subjectName').value.trim();
        const marks = document.getElementById('marks').value.trim();

        if (name == '' || subject == '' || marks == '') {
            alert('Please fill all the fields');
            return;
        }

        const namePattern = /^[A-Za-z\s]+$/;
        if (!namePattern.test(name)) {
            alert('Student name should only contain letters and spaces.');
            return;
        }

        if (!namePattern.test(subject)) {
            alert('Subject name should only contain letters and spaces.');
            return;
        }

        const marksPattern = /^[0-9]+$/; 
        if (!marksPattern.test(marks)) {
            alert('Marks should only contain digits.');
            return;
        }

        // Send the form data to PHP via AJAX
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'add_student.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function() {
            if (xhr.status === 200) {
                // Parse response and update the table dynamically if needed
                const response = JSON.parse(xhr.responseText);
                if (response.success) {
                    alert('Student added/updated successfully!');
                    location.reload(); // Reload the page to see updated table
                } else {
                    alert(response.message);
                }
            }
        };

        const params = `name=${name}&subject=${subject}&marks=${marks}`;
        xhr.send(params);
    });

    // Get modal and elements
    const modal = document.getElementById("addStudentModal");
    const closeBtn = document.querySelector(".btn-close");
    const name = document.getElementById('studentName');
    const subject = document.getElementById('subjectName');
    const marks = document.getElementById('marks');

    function openModal() {
        modal.style.display = "block";
    }

    // When the user clicks the close button
    closeBtn.onclick = function() {
        modal.style.display = "none";
        name.value = ""; 
        subject.value = "";
        marks.value = "";
    }

    // Close the modal if user clicks outside of it
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
            name.value = ""; 
            subject.value = "";
            marks.value = "";
        }
    }

</script>

</body>
</html>
