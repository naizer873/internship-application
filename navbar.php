	<nav class="navbar navbar-expand-sm bg-dark navbar-dark">
  		<!-- Brand -->
  		<a class="navbar-brand nav-link" href="dashboard.php"><?php echo $_SESSION['student_name']; ?></a>

  		<!-- Links -->
	  	<ul class="navbar-nav">
	    	<li class="nav-item">
	      		<a class="nav-link" href="profile.php">Profile</a>
	    	</li>
	    	<li class="nav-item">
	      		<a class="nav-link" href="application.php">My Application</a>
	    	</li>
	    	<li class="nav-item">
	      		<a class="nav-link" href="logout.php">Logout</a>
	    	</li>
	  	</ul>
	</nav>