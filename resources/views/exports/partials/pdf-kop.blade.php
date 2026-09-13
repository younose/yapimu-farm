<table style="width: 100%; border-collapse: collapse;">
    <tr>
        @if ($logoBase64)
            <td style="width: 64px; vertical-align: middle;">
                <img src="{{ $logoBase64 }}" style="height: 52px;">
            </td>
        @endif
        <td style="vertical-align: middle; text-align: {{ $logoBase64 ? 'left' : 'center' }}; padding-left: {{ $logoBase64 ? '10px' : '0' }};">
            <div style="font-size: 18px; font-weight: bold; text-transform: uppercase; color: #1d2939;">{{ $kop->name }}</div>
            @if ($kop->description)
                <div style="font-size: 10px; color: #475467;">{{ $kop->description }}</div>
            @endif
        </td>
    </tr>
</table>
<div style="border-bottom: 3px solid #1d2939; margin-top: 8px; margin-bottom: 4px;"></div>
<div style="border-bottom: 1px solid #1d2939; margin-bottom: 16px;"></div>
