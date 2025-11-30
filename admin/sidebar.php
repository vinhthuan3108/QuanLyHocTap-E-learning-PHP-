<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; transition: margin-left 0.3s; }
.main { margin-left: 220px; padding: 10px; transition: margin-left 0.3s; }
.main.full { margin-left: 60px; }
.sidebar {
    height: 100vh;
    position: fixed;
    width: 220px;
    background-color: #1f2937;
    color: #fff;
    padding-top: 20px;
    transition: width 0.3s;
    overflow-x: hidden;
}
.sidebar.closed { width: 60px; }
.sidebar h3, .sidebar a span { transition: opacity 0.3s; }
.sidebar.closed h3, .sidebar.closed a span { opacity: 0; }
.sidebar a {
    display: flex;
    align-items: center;
    color: #fff;
    padding: 12px 20px;
    text-decoration: none;
    margin-bottom: 5px;
    border-radius: 5px;
}
.sidebar a i { width: 25px; }
.sidebar a:hover { background-color: #374151; }
.main { margin-left: 220px; transition: margin-left 0.3s; }
.main.full { margin-left: 60px; }
.toggle-btn { position: fixed; top: 10px; left: 230px; z-index: 999; font-size: 20px; cursor: pointer; color: #1f2937; transition: left 0.3s; }
.toggle-btn.closed { left: 70px; }
</style>

<div class="sidebar" id="sidebar">
    <h3 class="text-center mb-4" id="sidebarToggle" style="cursor:pointer;">
        <i class="fa-solid fa-graduation-cap me-2"></i>
        <span>Admin</span>
    </h3>
    <a href="index.php"><i class="fa-solid fa-house"></i> <span>Dashboard</span></a>
    <a href="courses.php"><i class="fa-solid fa-book"></i> <span>Khóa học</span></a>
    <a href="teacher.php"><i class="fa-solid fa-chalkboard-teacher"></i> <span>Giáo viên</span></a>
    <a href="student.php"><i class="fa-solid fa-user-graduate"></i> <span>Học sinh</span></a>
    <a href="admin.php"><i class="fa-solid fa-user-shield"></i> <span>Quản trị hệ thống</span></a>
</div>

<script>
    const sidebarToggle = document.getElementById('sidebarToggle');

sidebarToggle.addEventListener('click', function() {
    sidebar.classList.toggle('closed');
    mainContent.classList.toggle('full');
    toggleBtn.classList.toggle('closed');
});

</script>
