<?php if(isset($_SESSION['alert'])): ?>

<div class="alert alert-<?php echo $_SESSION['alert']['class']; ?> alert-dismissible" role="alert">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
  <?php echo $_SESSION['alert']['text']; ?>
</div>

<script>
	setTimeout(function(){
		$(".alert").fadeOut(function(){
			$(this).remove();
		});
	}, 3000)
</script>

<?php unset($_SESSION['alert']); ?>
<?php endif;?>