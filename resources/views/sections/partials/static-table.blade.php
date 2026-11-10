<div class="tld-table-container reveal" tabindex="0" role="region" aria-label="{{ $table['table_label'] ?? '' }}">
  <table class="tld-table">
    <caption class="sr-only">@text($table['table_caption'] ?? ($table['table_label'] ?? ''))</caption>
    <thead>
      <tr>
        @foreach($table['table_headers'] ?? [] as $header)
          <th scope="col">@text($header)</th>
        @endforeach
      </tr>
    </thead>
    <tbody>
      @foreach($table['table_rows'] as $row)
        @php($cells = $row['cells'] ?? [])
        <tr>
          <th scope="row"><span class="tld-badge">@text($cells[0] ?? '')</span></th>
          @foreach(array_slice($cells, 1) as $cell)
            <td>@text($cell)</td>
          @endforeach
        </tr>
      @endforeach
    </tbody>
  </table>
</div>
