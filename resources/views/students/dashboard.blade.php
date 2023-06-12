@extends('app')

@section('title')
Dashboard
@endsection

@section('content')
    <div class="px-10 d-flex flex-column justify-content-center align-items-center main-content">
        <div class="form-group d-flex flex-column file-form">
                <p class="mb-1">Upload from file</p>
                <a class="mb-3" href="{{ route('students.template-download') }}">Download Excel Template</a>
                
                <label for="file" class="d-inline-block border-1 upload-file-container">
                    Choose File
                </label>
                <input type="file" name="file" id="file" class="form-control-file hide-button-file" accept=".csv">
                <p class="error-message text-danger"></p>

                <div class="mt-3 d-flex justify-content-between">
                    <button type="button" class="btn btn-primary flex-grow-1 add-tiny-right-space btn-upload">Upload</button>
                    <button type="button" class="btn btn-danger flex-grow-1 btn-cancel">Cancel</button>
                </div>

                <p class="uploading-error-message text-danger"></p>

                <div id="progress-bar-container" style="display: none;">
                    <div id="progress-bar"></div>
                </div>
        </div>

        <table class="table container px-5 mt-5">
            <thead class="thead-light">
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Name</th>
                    <th scope="col">Class</th>
                    <th scope="col">Level</th>
                    <th scope="col">Parent Contact</th>
                </tr>
            </thead>
            <tbody class="record-data">
            </tbody>
        </table>

        <div id="pagination">
        </div>

    </div>
@endsection

@section('scripts')
    let currentFile = null;
    let xhr = null;
    let page = 1;
    let totalPaginationBtn = 1;

    $('.btn-cancel').prop('disabled', true);

    $('.upload-file-container').click(function() {
        $('#file').change(function() {
            const selectedFile = this.files[0];

            if(selectedFile === undefined) {
                return;
            }

            if(selectedFile.type !== "text/csv") {
                $('.error-message').html("CSV file only");
            } else {
                $('.error-message').html("");
                $('.upload-file-container').html(selectedFile.name);
                currentFile = selectedFile;
                $(".uploading-error-message").html('');
                $('#progress-bar-container').css({display: 'none'});
            }
        });
    });

    $('.btn-upload').click(() => {
        if(currentFile === null || currentFile === undefined) {
            $('.error-message').html('no file to upload');
            return;
        }

        if(currentFile.type !== "text/csv") {
            $('.error-message').html("CSV file only");
            return;
        }

        $('.btn-cancel').prop('disabled', false);
        $('.btn-upload').prop('disabled', true);
        $(".uploading-error-message").html('');
        $('#progress-bar-container').css({display: 'initial'});
        uploadFile();
    });

    $('.btn-cancel').click(() => {

        if(xhr) {
            xhr.abort();
        }

        $('.btn-upload').prop('disabled', false);
        $('.btn-cancel').prop('disabled', true);
        $(".uploading-error-message").html('');
        $('#progress-bar-container').css({display: 'none'});
    });

    uploadFile = () => {

        const csrfToken = document.head.querySelector('meta[name="csrf-token"]').content;
        const formData = new FormData();
        formData.append('file', currentFile);
        formData.append('_token', csrfToken);

        xhr = new XMLHttpRequest();
        xhr.open('POST', "{{ route('students.file-upload') }}", true);

        xhr.upload.onprogress = function(e) {
        if (e.lengthComputable) {
            var percentComplete = (e.loaded / e.total) * 100;
            $('#progress-bar').css('width', percentComplete + '%');
        }
        };

        xhr.onload = function() {
            if (xhr.status === 200) {
                $(".uploading-error-message").addClass('text-success');
                $(".uploading-error-message").removeClass('text-danger');
            } else {
                $(".uploading-error-message").removeClass('text-success');
                $(".uploading-error-message").addClass('text-danger');
            }

            $('.btn-upload').prop('disabled', false);
            $('.btn-cancel').prop('disabled', true);

            const response = JSON.parse(xhr.responseText);
            $(".uploading-error-message").html(response.message);

            $('#progress-bar-container').css({display: 'none'});
            fetchStudentsRecords();
        };

        xhr.send(formData);
    }

    fetchStudentsRecords = () => {

        var url = "{{ route('students.students-records', ['page' => "S_PAGE"]) }}";
        url = url.replace('S_PAGE', page);

        $.ajax({
            url,
            type: 'GET',
            dataType: 'json',
            success: (response) => {
                let body = $('.record-data');
                body.empty();

                $.each(response.data, (index, item) => {
                    let row = `<tr>
                                    <td>${item.id}</td>
                                    <td>${item.name}</td>
                                    <td>${item.class}</td>
                                    <td>${item.level}</td>
                                    <td>${item.parent_contact}</td>
                                </tr>`;
                    body.append(row);
                });

                let pagination = $('#pagination');
                pagination.empty();
                totalPaginationBtn = 0;

                $.each(response.links, (index, item) => {
                    const isActive = item.active ? "btn btn-dark" : "btn btn-light";
                    let row = `<button type="button" id="pagination-btn-${index}" class="${isActive}">${item.label}</button>`;
                    pagination.append(row);
                    totalPaginationBtn++;
                    loadPaginationEventListener(index);
                });
                
            },
            error: (xhr, status, error) => {
                console.log(error);
            }
        })
    }

    loadPaginationEventListener = (index) => {
        $('#pagination-btn-' + index).on('click', () => {
            if($(this).text().includes('Previous')) {
                console.log('previus');
                if(page > 1) {
                    page--;
                }
            } else if ($(this).text().includes('Next')) {
                console.log('next');
                if(page < totalPaginationBtn) {
                    page++;
                }
            } else {
                page = parseInt($(this).text());
            }
            
            fetchStudentsRecords();
        });
    }

    fetchStudentsRecords();
@endsection