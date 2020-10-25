

/*
|--------------------------------------------------------------------------
| <?php echo $this->className."\n"; ?>
|--------------------------------------------------------------------------
*/

/* main - lists<?php echo $this->className; ?> */
Route::any('<?php echo $this->classNameLc; ?>/lists<?php echo $this->className; ?>', "<?php echo $this->className; ?>Controller@get<?php echo $this->className; ?>List");

/* main - edit<?php echo $this->className; ?> */
Route::any('<?php echo $this->classNameLc; ?>/edit<?php echo $this->className; ?>', "<?php echo $this->className; ?>Controller@edit<?php echo $this->className; ?>");

/* main - delete<?php echo $this->className; ?> */
Route::any('<?php echo $this->classNameLc; ?>/delete<?php echo $this->className; ?>', "<?php echo $this->className; ?>Controller@delete<?php echo $this->className; ?>");