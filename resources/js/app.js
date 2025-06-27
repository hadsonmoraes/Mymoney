import './bootstrap';
import './custom';
import 'summernote/dist/summernote-lite';
import 'summernote/dist/summernote-lite.css';

$(document).ready(function(){
    $('#summernote').summernote({
    height: 150, // set editor height
    minHeight: 100, // set minimum height of editor
    maxHeight: 250, // set maximum height of editor
    toolbar: [
        ['style', ['style']],
        ['font', ['bold', 'underline', 'clear']],
        ['color', ['color']],
        ['para', ['ul', 'ol', 'paragraph']],
        ['table', ['table']],
        ['insert', ['link', 'picture', 'video']],
        ['view', [ 'codeview', 'help']]
    ],
    });
});

import Swal from 'sweetalert2';
window.Swal = Swal;
