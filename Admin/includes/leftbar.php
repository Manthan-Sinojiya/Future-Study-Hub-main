<div class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
        <li class="nav-item nav-profile">
            <a href="./dashboard.php" class="nav-link">
                <div class="nav-profile-image">
                    <img src="https://bootdey.com/img/Content/avatar/avatar7.png" alt="profile">
                    <span class="login-status online"></span>
                </div>
                <div class="nav-profile-text">
                    <span class="font-weight-bold mb-2">Admin</span>
                    <span class="text-secondary text-small">Administrator</span>
                </div>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="dashboard.php">
                <span class="menu-title">Dashboard</span>
                <i class="mdi mdi-home menu-icon"></i>
            </a>
        </li>
        <li class="nav-item">
              <a class="nav-link" data-bs-toggle="collapse" href="#teacher" aria-expanded="false" aria-controls="teacher">
                <span class="menu-title">Teacher</span>
                <i class="menu-arrow"></i>
                <i class="mdi mdi-account-box menu-icon"></i>
              </a>
              <div class="collapse" id="teacher">
                <ul class="nav flex-column sub-menu">
                  <li class="nav-item"> <a class="nav-link" href="admin_teacher_approvals.php">Add Teachers</a></li>
                  <li class="nav-item"> <a class="nav-link" href="manage-teachers.php">Manage Teachers</a></li>
                </ul>
              </div>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="manage-students.php">
                <span class="menu-title">Manage Students</span>
                <i class="mdi mdi-school menu-icon"></i>
            </a>
        </li>
        <li class="nav-item">
              <a class="nav-link" data-bs-toggle="collapse" href="#student" aria-expanded="false" aria-controls="student">
                <span class="menu-title">Courses</span>
                <i class="menu-arrow"></i>
                <i class="mdi mdi-book-plus menu-icon"></i>
              </a>
              <div class="collapse" id="student">
                <ul class="nav flex-column sub-menu">
                  <li class="nav-item"> <a class="nav-link" href="add-course.php">Add Course</a></li>
                  <li class="nav-item"> <a class="nav-link" href="manage-courses.php">Manage Courses</a></li>
                </ul>
              </div>
        </li>
        <li class="nav-item">
              <a class="nav-link" data-bs-toggle="collapse" href="#notice" aria-expanded="false" aria-controls="notice">
                <span class="menu-title">Notices</span>
                <i class="menu-arrow"></i>
                <i class="mdi mdi-message-plus menu-icon"></i>
              </a>
              <div class="collapse" id="notice">
                <ul class="nav flex-column sub-menu">
                  <li class="nav-item"> <a class="nav-link" href="add-notice.php">Add Notice</a></li>
                  <li class="nav-item"> <a class="nav-link" href="manage-notices.php">Manage Notices</a></li>
                </ul>
              </div>
        </li>
        <li class="nav-item">
              <a class="nav-link" data-bs-toggle="collapse" href="#report" aria-expanded="false" aria-controls="report">
                <span class="menu-title">Report</span>
                <i class="menu-arrow"></i>
                <i class="mdi mdi-chart-arc menu-icon"></i>
              </a>
              <div class="collapse" id="report">
                <ul class="nav flex-column sub-menu">
                  <li class="nav-item"> <a class="nav-link" href="tps.php">TPS Report</a></li>
                  <li class="nav-item"> <a class="nav-link" href="mis.php">MIS Reports</a></li>
                </ul>
              </div>
        </li>
    </ul>
</div>
