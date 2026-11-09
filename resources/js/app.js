import Alpine from 'alpinejs';
import registerUi from './ui';
import './bootstrap';
import 'select2/dist/css/select2.min.css';
import './custom';
import 'summernote/dist/summernote-lite';
import 'summernote/dist/summernote-lite.css';

window.Alpine = Alpine;
registerUi(Alpine);
Alpine.start();

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
