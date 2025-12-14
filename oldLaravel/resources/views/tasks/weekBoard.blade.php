@if( ( (Auth::user()->role_id == "1") || (Auth::user()->role_id == "2") ) )
<!--<table class="table table-striped table_font no_print">
  <thead>
    <tr>
      <th>Lunes </th>
      <th>Martes </th>
      <th>Miércoles </th>
      <th>Jueves </th>
      <th>Viernes </th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>MAQUI</td>
      <td>PRANHA</td>
      <td>PEQUE</td>
      <td>MAQUI, PRANHA</td>
      <td>My SEO</td>
    </tr>
  </tbody>
</table>-->

<table class="table table-striped table_font no_print">
  <thead>
    <tr>
      <th>Lunes</th>
      <th>Martes</th>
      <th>Miércoles</th>
      <th>Jueves</th>
      <th>Viernes</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>MAQUI</td>
      <td>PRANHA</td>
      <td>PEQUE</td>
      <td>MAQUI, PRANHA</td>
      <td>My SEO</td>
    </tr>
  </tbody>
</table>

@endif