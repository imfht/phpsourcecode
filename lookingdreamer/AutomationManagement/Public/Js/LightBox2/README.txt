<<Modified by Jeff Garczewski http://www.punkbyte.com 2/28/06
To work with new captions feature and bug fixes>>

<<Original Mod by Kelli Shaver http://www.kellishaver.com>>


This is a very small and simple modification to Lightbox to allow you
to use the script in conjunction with a flash movie (for a sample of
this see www.kellishaver.com).

I've added a new function flashLightBox() that can be called from within
any SWF file. I've left showLightbox() in thre as well, so the script
can still be used normally.


To call a lightbox image from flash, you would simply assign the
following actionscript to your button/movie clip:

<<Added by JG: You can now use the new caption feature of lightbox by sending a second variable along to the flashLightBox function>>

on(release) {
    getURL("javascript:flashLightbox('path/to/imagejpg','Your Caption');");
}


I've also modofied the default CSS to give the lightbox containers and
image a higher z-index than the flash movie.

Finally, to get the images to display on top of the movie, you will need
to add the "wmode" paramater to the mark-up for embedding the SWF movie:

<object type="application/x-shockwave-flash" data="myMovie.swf" width="400" height="400">
    <param name="movie" value="myMovie.swf" />

    <!-- Your MUST add the 'wmode' paramater or your flash will appear
         over top of your lightbox images! -->

    <param name="wmode" value="transparent" />
</object>

This wasn't a difficult mod, but it did take me a few minutes to sort out, 
since there were several files to change, so hopefully this will be of
use to someone.