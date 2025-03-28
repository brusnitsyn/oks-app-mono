<table>
    <thead>
    <tr>
        <th>Дата рождения</th>
        <th>ФИО</th>
        <th>4-й день</th>
        <th>1-й месяц</th>
        <th>3-й месяц</th>
        <th>6-й месяц</th>
        <th>12-й месяц</th>
    </tr>
    </thead>
    <tbody>
    @foreach($patients as $patient)
        <tr>
            <td>{{ \Illuminate\Support\Carbon::createFromTimestampMs($patient->brith_at)->format('d.m.Y') }}</td>
            <td>{{ $patient->full_name }}</td>
            <td>{{ \Illuminate\Support\Carbon::createFromTimestampMs($patient->lastMedcard->day3->call_at)->format('d.m.Y') }}</td>
            <td>{{ \Illuminate\Support\Carbon::createFromTimestampMs($patient->lastMedcard->mes1->call_at)->format('d.m.Y') }}</td>
            <td>{{ \Illuminate\Support\Carbon::createFromTimestampMs($patient->lastMedcard->mes3->call_at)->format('d.m.Y') }}</td>
            <td>{{ \Illuminate\Support\Carbon::createFromTimestampMs($patient->lastMedcard->mes12->call_at)->format('d.m.Y') }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
