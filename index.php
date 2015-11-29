<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Web Scraper</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
	<style> 
		body {
		  min-height: 2000px;
		}

		.navbar-static-top {
		  margin-bottom: 19px;
		}	
	</style>
  </head>
  <body>
		<div class="container">
		
			<div class="jumbotron">
				<form>
					<div class="form-group">
						<label for="url">Enter URL to scrape (i.e., www.linkedin.com/name...)</label>
						<input type="text" class="form-control" id="url" name="url" placeholder="i.e., www.linkedin.com/dominic...." autofocus>
					</div>
					<div class="form-group">
						<label for="fields">Enter specific fields to scrape separated by comma (i.e., Firstname, Lastname, etc.)</label>
						<input type="text" class="form-control" id="fields" name="fields" placeholder="i.e., Firstname, Lastname, etc.">
					</div>
					<button type="submit" id="btnSubmit" class="btn btn-primary" onclick="scrapeSubmit(); return false;">Scrape</button>
					| <a href="index.php">Clear</a>
				</form>
			</div>
			
			<div id="output-box" class="panel panel-default">
				<div class="panel-heading">Scrape Output:</div>
				<div class="panel-body" id="scrape-ouput">
				No Scrape yet.
				</div>
			</div>
			
		</div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
	
	<script>
		function scrapeSubmit() {
			
			var url    = $.trim($('#url').val()),
				fields = $.trim($('#fields').val()),
				submit = $('#btnSubmit'),
				output = $('#scrape-ouput');
			
			if( url != '' ) {
				
				submit.attr('disabled', 'disabled');
				submit.text('Please wait...');	
				$('#output-box').removeAttr('class').attr('class', 'panel panel-default');
				output.text('Scraping...');	
				
				$.post("ajax.php", { url: url, fields: fields })
					.done(function(data) {
						$('#btnSubmit').removeAttr('disabled').text('Scrape');												
						$('#output-box').removeAttr('class').attr('class', 'panel panel-success');
						output.html(data);				
					})
					.fail(function(data) {
						$('#btnSubmit').removeAttr('disabled').text('Scrape');	
						$('#output-box').removeAttr('class').attr('class', 'panel panel-danger');
						output.text(data.status + ' ' + data.statusText);
					});	
			} else
				$('#url').focus();
		}
	</script>
  </body>
</html>