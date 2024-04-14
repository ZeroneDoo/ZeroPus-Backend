@extends('template.layout')

@section('title')
    Book - ZeroPus
@endsection

@section('header_title')
    Book
@endsection

@section('content')
<div class="card my-4" style="width: 100%; border-radius: 20px">
    <div class="card-body">
        {{-- <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckDefault" />
        </div> --}}
        <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#modal">
            New Book
        </button>
        <div class="table-responsive">
            <table id="table" class="table">

            </table>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document" style="width: 100%">
                <div class="modal-content">
                    <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Create or Update Book</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    </div>
                    <form id="form" action="{{ route('book.store') }}" method="POST" enctype="multipart/form-data">
                        <div class="modal-body row">
                            @csrf
                            <div class="col">
                                <div class="mb-3">
                                    <label for="title">Title<sup style="color: red">*</sup></label>
                                    <input type="text" class="form-control" name="title" id="title" required>
                                </div>
                                <div class="mb-3">
                                    <label for="description">Description<sup style="color: red">*</sup></label>
                                    <textarea class="form-control" name="description" id="description" required></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="penulis">Penulis<sup style="color: red">*</sup></label>
                                    <input type="text" class="form-control" name="penulis" id="penulis" required>
                                </div>
                                <div class="mb-3">
                                    <label for="penerbit">Penerbit<sup style="color: red">*</sup></label>
                                    <input type="text" class="form-control" name="penerbit" id="penerbit" required>
                                </div>
                                <div class="mb-3">
                                    <label for="amount">Amount<sup style="color: red">*</sup></label>
                                    <input type="text" class="form-control" name="amount" id="amount" required>
                                </div>
                                <div class="mb-3">
                                    <label for="stock">Stock<sup style="color: red">*</sup></label>
                                    <input type="number" class="form-control" name="stock" id="stock" required>
                                </div>
                                <div class="mb-3">
                                    <label for="tahun_terbit">Tahun Terbit<sup style="color: red">*</sup></label>
                                    <input type="text" class="form-control" name="tahun_terbit" id="tahun_terbit" required>
                                </div>
                                <div class="mb-3">
                                    <label for="category">Category<sup style="color: red">*</sup></label>
                                    <select class="form-select" id="category" name="category[]" data-placeholder="Choose anything" multiple required>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach 
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="source">Source</label>
                                    <input type="file" class="form-control mb-2" name="source" id="source" accept="application/pdf">
                                    <div id="resultSource">
                                        {{--  --}}
                                    </div>
                                </div>
                                <div class="mb-3 container">
                                    <div class="form-check form-switch">
                                        <label for="is_rent" style="display: flex; align-items: center; gap: 10px"><input class="form-check-input" type="checkbox" role="switch" id="is_rent" state="true" name="is_rent"><p style="margin: 0">Is Rent</p></label>
                                    </div>
                                </div>
                            </div>
                            {{-- file --}}
                            <div class="col">
                                <div class="mb-3">
                                    <label for="photo">Photo<sup style="color: red">*</sup></label>
                                    <div>
                                        <div class="mb-4 d-flex justify-content-center">
                                            <img id="selectedImage" src="https://mdbootstrap.com/img/Photos/Others/placeholder.jpg" 
                                            alt="example placeholder" style="width: 300px;border-radius: 10px" />
                                        </div>
                                        <div class="d-flex justify-content-center">
                                            <div class="btn btn-primary btn-rounded">
                                                <label class="form-label text-white m-1" style="cursor: pointer" for="photo">Choose file</label>
                                                <input type="file" accept="image/*" class="form-control d-none" id="photo" name="photo" onchange="displaySelectedImage(event, 'selectedImage')" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" id="btn-close" data-dismiss="modal">Close</button>
                            <button class="btn btn-primary" id="btn-submit">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>    
@endsection

@push('js')
    <script>
        let table
        const form = $("#form")

        $(document).ready( () => {
            table = $("#table").DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('book.getData') }}",
                    type: 'GET',
                    dataSrc: "data"
                },
                columns: [
                    {
                        data: 'action',
                        title: 'Action',
                    },
                    { data: 'title', title: 'Title', orderable: true },
                    { data: 'penulis', title: 'Penulis', orderable: false },
                    { data: 'penerbit', title: 'Penerbit',orderable: false },
                    { 
                        data: 'amount', 
                        title: 'Amount',
                        orderable: false,
                        render: (data, type, row, meta) => {
                            return `
                            <p>${row.amount.toLocaleString('id', {minimumFractionDigits: 0})}P</p>
                            `
                        }
                    },
                    { 
                        data: 'is_rent', 
                        title: 'Is Rent',
                        orderable: false, 
                        render: (data, type, row, meta) => {
                            return `
                            <form class="container">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_rent_live" ${row.is_rent ? "checked" : ""} state="true" role="switch" data-id="${row.id}" onchange="changeRent(this)">
                                </div>
                            </form>
                            `
                        }
                    },
                ]
            })

            setTimeout(() => {
                console.log(table.data().toArray())
            }, 3000);

            form.on("submit", (e) => {
                e.preventDefault()
                $("#is_rent").val($("#is_rent").is(":checked"))
                let data = new FormData($("#form")[0]);

                $.ajax({
                    type: form.attr("method"),
                    url: form.attr("action"),
                    data: data,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    processData: false,
                    contentType: false,
                    beforeSend: () => {
                        $("#btn-submit").attr("disabled", true)
                        $("#btn-close").attr("disabled", true)
                        $("#btn-submit").html(`<label for="" style="display: flex; align-items: center; gap: 10px;margin: 0"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" id="loader"></span><p style="margin: 0">Save changes</p></label>`)
                    },
                    success: (res, textStatus, xhr) => {
                        table.ajax.reload()
                        clearModal()
                        showAlert("Success to create or update book", "success")
                    },
                    error: () => {
                        $("#btn-submit").removeAttr("disabled")
                        $("#btn-close").removeAttr("disabled")
                        $("#btn-submit").html("Save Changes")
                        showAlert("Failed to create or update book", "error")
                    }
                });
            })
        });
        // 
        $("#tahun_terbit").datepicker({
            format: "yyyy",
            viewMode: "years", 
            minViewMode: "years"
        });

        // category
        $( '#category').select2( {
            theme: "bootstrap-5",
            width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
            placeholder: $( this ).data( 'placeholder' ),
            closeOnSelect: false,
        } );

        const displaySelectedImage = (event, elementId) => {
            const selectedImage = document.getElementById(elementId);
            const fileInput = event.target;

            if (fileInput.files && fileInput.files[0]) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    selectedImage.src = e.target.result;
                };

                reader.readAsDataURL(fileInput.files[0]);
            }
        }

        const changeRent = (button) => {
            const id = $(button).attr("data-id") 

            let action = "{{ route('book.update', ':id') }}"
            action = action.replace(':id', id)

            $.ajax({
                type: "POST",
                url: action,
                data: JSON.stringify({
                    is_rent: $(button).is(":checked")
                }),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    "Content-Type": "application/json"
                },
                beforeSend: () => {
                    $(button).attr("disabled", true)
                },
                success: (res) => {
                    table.ajax.reload()
                    $(button).removeAttr("disabled")
                },
                error: () => {
                    $(button).removeAttr("disabled")
                }
            });
        }

        const getData = (button) => {
            const index = table.row($(button).closest('tr')).index()
            const dataTable = table.row(index).data()

            let action = "{{ route('book.update', ':id') }}"
            action = action.replace(':id', dataTable['id'])
            let source = dataTable['source'] 
            let indexSoure = source.indexOf('source/')
            source = source.substring(indexSoure + 7)
            

            console.log(dataTable)
            form.attr("action", action)
            form.find("input[name='title']").val(dataTable['title'])
            form.find("textarea[name='description']").text(dataTable['description'])
            form.find("input[name='penulis']").val(dataTable['penulis'])
            form.find("input[name='penerbit']").val(dataTable['penerbit'])
            form.find("input[name='amount']").val(dataTable['amount'])
            form.find("input[name='stock']").val(dataTable['stock'])
            form.find("input[name='tahun_terbit']").val(dataTable['tahun_terbit'])
            form.find("input[name='is_rent']").prop("checked",dataTable['is_rent'])
            $('#category').val(dataTable['category']).change();
            form.find("#selectedImage").attr("src",dataTable['photo'])
            if(indexSoure !== -1) {
                form.find("#resultSource").html(`
                <div style="border: 1px solid #d1d3e2; padding:5px; border-radius: 5px; display: flex; align-items: center; gap:10px">
                    <i class="fas fa-solid fa-file" style="color:red"></i>
                    <p style="margin: 0">${source}</p>
                </div>
                `)
            }else{
                form.find("#resultSource").html(``)
            }
        }

        const deleteData = (e) => {
            let el = $(e);
            let id = el.data("id");
            $.ajax({
                type: "DELETE",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: `credit/delete/${id}`,
                beforeSend: () => {
                    el.html(`<label for="" style="display: flex; align-items: center; gap: 10px;margin: 0"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" id="loader"></span></label>`)
                    el.attr("disabled", true)
                },
                success: (res, textStatus, xhr) => {
                    table.ajax.reload()
                    showAlert("Success to delete book", "success")
                },
                error: () => {
                    showAlert("Failed to delete book", "error")
                }
            });
        };

        const clearModal = () => {
            $("#btn-submit").removeAttr("disabled")
            $("#btn-close").removeAttr("disabled")
            $("#btn-submit").html("Save Changes")
            $("#selectedImage").attr("src","https://mdbootstrap.com/img/Photos/Others/placeholder.jpg")
            $("#is_rent").prop("checked", false)

            $("#modal").modal("hide")
            $('#form input').val('')
            $('#form textarea').text('')
            $('#category').val(null).trigger('change')

            form.attr("action", "{{ route('book.store') }}")
        };
        $('[data-target="#modal"]').on('click', () => {
            clearModal()
        });
    </script>
@endpush