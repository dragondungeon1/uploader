/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';
import './bootstrap';
import "dropzone/dist/dropzone.css";
// import * as Dropzone from 'dropzone'
import Dropzone from 'dropzone'

Dropzone.autoDiscover = false;
document.addEventListener('DOMContentLoaded', () => {
    let myDropzone = new Dropzone(".dropzone2", {
        url: '/_uploader/gallery/upload',
        sending: () => {
            console.log('sending')
        },
        success: () => {
            console.log('success')
        },
        parallelUploads: 4,
        chunking: true,
        forceChunking: true,
        chunkSize: 3000000,
        retryChunks: true,
        maxFilesize: 102400,
        //renameFile: true,
        retryChunksLimit: 3,

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

    myDropzone.on("addedfile", file => {
        console.log("A file has been added");
        console.log(file);
    });

});
