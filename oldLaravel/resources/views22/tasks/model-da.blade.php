<div class="modal fade" id="taskStatusesModal" tabindex="-1" role="dialog" aria-labelledby="taskStatusesModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="taskStatusesModalLabel">Task Statuses</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          <table class="table">
            <thead class="thead-dark">
              <tr>
                <th scope="col" style="border-left: 10px solid #343a40 !important">Name</th>
                <th scope="col" style="border-left: 10px solid #343a40 !important">Description</th>
              </tr>
            </thead>
            <tbody>
              @foreach($task_status as $status_option)
              <tr>
                <th scope="row" style="border-left: 10px solid {{ $status_option->background_color }} !important;">{{ $status_option->name }}</th>
                <td scope="row">{{ $status_option->description }}</td>
              </tr>
              @endforeach
            </tbody>
          </table>
      </div>
    </div>
  </div>
</div>