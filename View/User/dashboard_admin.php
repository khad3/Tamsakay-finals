<!DOCTYPE html>
<html lang="en">
<head>
  <title>Bootstrap Example</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <style>
    /* Set height of the grid so .sidenav can be 100% (adjust as needed) */
    .row.content {height: 550px}
    
    /* Set gray background color and 100% height */
    .sidenav {
      background-color: #f1f1f1;
      height: 100%;
    }
        
    /* On small screens, set height to 'auto' for the grid */
    @media screen and (max-width: 767px) {
      .row.content {height: auto;} 
    }
  </style>
</head>
<body>

<nav class="navbar navbar-inverse visible-xs">
  <div class="container-fluid">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>                        
      </button>
      <a class="navbar-brand" href="#">Tamsakay</a>
    </div>
    <div class="collapse navbar-collapse" id="myNavbar">
      <ul class="nav navbar-nav">
        <li class="active"><a href="#">Dashboard</a></li>
        <li><a href="#">Settings</a></li>

      </ul>
    </div>
  </div>
</nav>

<div class="container-fluid">
  <div class="row content">
    <div class="col-sm-3 sidenav hidden-xs">
      <h2>Logo</h2>
      <ul class="nav nav-pills nav-stacked">
        <li class="active"><a href="#section1">Dashboard</a></li>
        <li><a href="#section2">Settings</a></li>

      </ul><br>
    </div>
    <br>
    
    <div class="col-sm-9">
      <div class="well">
        <h2>HED</h2>
        <p>500 students</p>
      </div>
      <div class="row">
        <div class="col-sm-3">
          <div class="well">
            <h2>BED</h2>
            <p>1 student</p> 
          </div>
        </div>
        <div class="col-sm-3">
          <div class="well">
            <h2>MAIN GATE</h2>
            <p>100 students</p> 
          </div>
        </div>
        <div class="col-sm-3">
          <div class="well">
          <h3>  Status : </h3>
          <button type="submit" name = "NA" class="btn btn-danger">NOT AVAILABLE</button>
          <button type="submit" name = "OTW" class="btn btn-success">OTW</button>
          <button type="submit" name = "ON_BREAK" class="btn btn-warning">ON BREAK</button>
          </div>
        </div>
      
        </div>
      </div>
    </div>
  </div>
</div>


</body>
</html>
