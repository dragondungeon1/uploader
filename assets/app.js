/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';
import './bootstrap';
// import * as Dropzone from 'dropzone'
import Dropzone from 'dropzone'

Dropzone.autoDiscover = false;
document.addEventListener('DOMContentLoaded', () => {
    let myDropzone = new Dropzone(".dropzone2", {
        url: '/handle-file',
        sending: () => {
            console.log('sending')
        },
        success: () => {
            console.log('success')
        },
        parallelUploads: 1,
        chunking: true,
        forceChunking: true,
        chunkSize: 10000,
        retryChunks: true,
        maxFilesize: 102400,

        // chunksUploaded: (file, done) => {
        //     console.log(file)
        //     console.log(done)
        //
        //     const data = new URLSearchParams()
        //     data.append('file', file)
        //
        //     fetch('/handle-file', {
        //         method: 'POST',
        //         data: data
        //     })
        //         .then((response) => {
        //             console.log(response);
        //         })
        //         // .then(file => console.log(file))
        // },
    });

});
