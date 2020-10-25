/**
 * Created by targaryen on 16-9-4.
 */
import 'bootstrap';
import 'lazysizes';
import './iconfont';

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
