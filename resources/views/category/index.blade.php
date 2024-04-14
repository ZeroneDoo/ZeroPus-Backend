@extends('template.layout')

@section('title')
    Category - ZeroPus
@endsection

@section('header_title')
    Category
@endsection

@section('content')
    <div class="card my-4" style="width: 100%; border-radius: 20px">
        <div class="card-body">
            <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#modal">
                New Category
            </button>
            <div class="table-responsive">
                <table id="table" class="table">
    
                </table>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Create or Update Category</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    </div>
                    <form id="form" action="{{ route('category.store') }}" method="POST">
                        <div class="modal-body">
                            @csrf
                            <div class="mb-3">
                                <label for="name">Name<sup style="color: red">*</sup></label>
                                <input type="text" class="form-control" name="name" id="name" required>
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
                    url: "{{ route('category.getData') }}",
                    type: 'GET',
                    dataSrc: "data"
                },
                columns: [
                    {
                        data: 'action',
                        title: 'Action',
                    },
                    { data: 'name', title: 'Name', orderable: true },
                    { data: 'created_at', title: 'Created At', orderable: true },
                    { data: 'updated_at', title: 'Updated At', orderable: true },
                ]
            })

            setTimeout(() => {
                console.log(table.data().toArray())
            }, 3000);

            form.on("submit", (e) => {
                e.preventDefault()
                let data = new FormData($("#form")[0]);
                // kirim data dari form
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
                        showAlert("Success to create or update category", "success")
                    },
                    error: () => {
                        $("#btn-submit").removeAttr("disabled")
                        $("#btn-close").removeAttr("disabled")
                        $("#btn-submit").html("Save Changes")
                        showAlert("Failed to create or update category", "error")
                    }
                });
            })
        });

        // show
        const getData = (button) => {
            const index = table.row($(button).closest('tr')).index()
            const dataTable = table.row(index).data()

            let action = "{{ route('category.update', ':id') }}"
            action = action.replace(':id', dataTable['id'])

            console.log(dataTable)
            form.attr("action", action)
            form.find("input[name='name']").val(dataTable['name'])
        }

        const deleteData = (e) => {
            let el = $(e);
            let id = el.data("id");
            $.ajax({
                type: "DELETE",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: `category/delete/${id}`,
                beforeSend: () => {
                    el.html(`<label for="" style="display: flex; align-items: center; gap: 10px;margin: 0"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" id="loader"></span></label>`)
                    el.attr("disabled", true)
                },
                success: (res, textStatus, xhr) => {
                    table.ajax.reload()
                    showAlert("Success to delete category", "success")
                },
                error: () => {
                    showAlert("Failed to delete category", "error")
                }
            });
        };

        const clearModal = () => {
            $("#btn-submit").removeAttr("disabled")
            $("#btn-close").removeAttr("disabled")
            $("#btn-submit").html("Save Changes")

            $("#modal").modal("hide")
            $('#form input').val('')

            form.find("input[name='is_active']").val("checked")
            form.attr("action", "{{ route('category.store') }}")
        };
        $('[data-target="#modal"]').on('click', () => {
            clearModal()
        });
    </script>
@endpush