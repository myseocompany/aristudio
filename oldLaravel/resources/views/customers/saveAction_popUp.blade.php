<!-- Button to Open the Modal -->
<button type="button"  data-toggle="modal" class="btn btn-link" data-target="#saleModal{{ $item->id }}" value="{{ $item->id }}"><img src="img/accion.png"  width="20"></button>

<form action="/customers/{{ $item->id }}/action/save" method="POST" >
<!-- The Modal -->
<div class="modal" id="saleModal{{ $item->id }}">
  <div class="modal-dialog">
    <div class="modal-content"  value="{{ $item->id }}" >

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Agregar acción</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

   
        <input type="hidden" id="customer_id" name="customer_id" value="{{ $item->id }}">
        
        

        <!-- Modal body -->
        <div class="modal-body">
          <div class="form-group">
            <label for="sale_date">Tipo de acción</label><br>
            <select name="type_id" id="type_id" required>
    @foreach($action_options as $item)
    <option value="{{ $item->id }}"  @if(isset($pending_action)&&($item->id==$pending_action->type_id))selected="selected"@endif> {{$item->name}}</option>
    @endforeach
  </select>
  <select name="status_id" id="status_id">
    <option value="">Seleccione un estado</option>
    @foreach($statuses_options as $status_option)
    <option value="{{$status_option->id}}">{{$status_option->name}}</option>
    @endforeach
  </select>
          </div>

          


          <div class="form-group">
            <textarea name="note" id="note" cols="50" rows="5" required="required"></textarea>
          </div>


        <!-- Modal footer -->
        <div class="modal-footer">
        <div>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
          <input class="btn btn-primary" type="submit" value="Enviar acción">
          
        </div>
          
        </div>
     </form>

    </div>
  </div>
</div>

<style>
  .info{
display: flex;
flex-direction: row;
justify-content: center;
align-items: center;
padding: 6px 38px;
gap: 10px;

position: relative;
width: 200px;
height: 45px;

background: #17A2B8;
}
</style>