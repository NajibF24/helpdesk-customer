<div class="custom-modal fade" id="materialDetailModal" tabindex="-1" aria-labelledby="materialDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="materialDetailModalLabel">Material Detail</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true" style="display: block">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-5">
                    <div class="group-item">
                        <strong>Name</strong>
                        <p id="name"></p>
                    </div>
                    <div class="group-item">
                        <strong>PO Number</strong>
                        <p id="po_number"></p>
                    </div>
                    <div class="group-item">
                        <strong>Warehouse</strong>
                        <p id="warehouse"></p>
                    </div>
                    <div class="group-item">
                        <strong>Store Location</strong>
                        <p id="store_location"></p>
                    </div>
                    <div class="group-item">
                        <strong>Material Code</strong>
                        <p id="material_code"></p>
                    </div>
                    <div class="group-item">
                        <strong>Material Code Name</strong>
                        <p id="material_code_name"></p>
                    </div>
                    <div class="group-item">
                        <strong>Group</strong>
                        <p id="group"></p>
                    </div>
                    <div class="group-item">
                        <strong>UoM</strong>
                        <p id="uom"></p>
                    </div>
                    <div class="group-item">
                        <strong>Serial Number</strong>
                        <p id="serial_number"></p>
                    </div>
                    <div class="group-item">
                        <strong>Brand</strong>
                        <p id="brand"></p>
                    </div>
                    <div class="group-item">
                        <strong>Material Tag</strong>
                        <p id="material_tag"></p>
                    </div>
                    <div class="group-item">
                        <strong>Qty</strong>
                        <p id="qty"></p>
                    </div>
                    <div class="group-item">
                        <strong>Description</strong>
                        <p id="description"></p>
                    </div>
                    <div class="group-item">
                        <strong>Specification</strong>
                        <p id="specification"></p>
                    </div>
                </div>
                <div class="col-md-7">
                    <img id="image" src="" alt="material detail image" style="width: 100%">
                </div>
            </div>
        </div>
      </div>
    </div>
</div>

<script>
    $('.show-material').click(async function() {
        $('#materialDetailModal').modal('show')
        $('#materialDetailModal').removeClass('custom-modal')
        $('#materialDetailModal').addClass('modal')

        const materialId = $(this).data('material_id')
        let data = {}

        const res = await $.getJSON(`{{url("/")}}/material/detail/${materialId}`)
            .done(res => {
                data = res.data
            }).fail(() => {
                alert('Something went wrong')
            })

        $('#name').text(data.name)
        $('#po_number').text(data.po_number)
        $('#warehouse').text(data.store_location?.warehouse?.name)
        $('#store_location').text(data.store_location?.name)
        $('#material_code').text(data.material_code?.code)
        $('#material_code_name').text(data.material_code?.name)
        $('#group').text(data.material_code?.material_group?.name)
        $('#uom').text(data.material_code?.uom_label)
        $('#serial_number').text(data.serial_number)
        $('#brand').text(data.brand.name)
        $('#material_tag').text(data.material_tag)
        $('#qty').text(data.qty)
        $('#description').text(data.description)
        $('#specification').text(data.specification)
        if(data.image) {
            $('#image').attr('src', '{{asset("")}}'+data.image)
        } else {
            $('#image').attr('src', '{{asset("assets/img/no-image.jpeg")}}')
        }
    })
</script>