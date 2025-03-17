<table class="table">
    <tbody>
        <thead>
            <tr>
                <th width="5%"></th>
                <th width="10%">kubun_id</th>
                <th width="45%">kubun_value</th>
                <th width="10%">notes</th>
                <th width="10%" class="text-center">sort_no</th>
                <th width="20%" class="text-right">action</th>
            </tr>
        </thead>

        @foreach($kubun_type as $value)
        <tr>
            <td>
                <input type="checkbox" class="checkbox" value="{{ $value->kubun_id }}">
            </td>
            <td class="text-left kubun_id">{{ $value->kubun_id }}</td>
            <td class="text-left">
                <a class="kubun_value">{{ $value->kubun_value }}</a>
            </td>
            <td class="text-center">{{ $value->notes }}</td>
            <td class="text-center sort_no">{{ $value->sort_no }}</td>

            <td class="text-right">
                @if($value->sort_no == 1)
                    <i class="fas fa-arrow-left fa-rotate-90 btn-up" style="display: none;"></i>
                @else
                    <i class="fas fa-arrow-left fa-rotate-90 btn-up"></i>
                @endif

                @if($value->sort_no == (count($kubun_type)))
                    <i class="fas fa-arrow-right fa-rotate-90 btn-down" style="display: none;"></i>
                @else
                    <i class="fas fa-arrow-right fa-rotate-90 btn-down"></i>
                @endif

            </td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td class="top-align">
                <input type="checkbox" id="check_all" value="">
            </td>
            <td colspan="5">
                <button type="button" class="btn btn-sm update-edit color-secondary" id="btn-delete" style="display: none;">Delete</button>
                <button type="button" class="btn btn-sm update-edit color-secondary" id="btn-update" style="display: none;">Change</button>
                <button type="button" class="btn btn-sm color-primary" id="new">New</button>
            </td>
        </tr>
    </tfoot>
</table>
