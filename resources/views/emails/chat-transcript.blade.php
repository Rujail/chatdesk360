@component('mail::message')
# Your Chat Transcript

Thank you for chatting with us! Below is the transcript of your recent conversation.

@if(count($messages) > 0)
<div style="font-family: Arial, sans-serif; color: #333; width: 100%; padding: 10px 0;">
@foreach($messages as $msg)

@if($msg['type'] === 'visitor')
<div style="width: 100%; margin: 8px 0; text-align: right;">
<div style="display: inline-block; text-align: left; background-color: #2b60d0; color: #ffffff; padding: 10px 15px; border-radius: 16px 16px 4px 16px; font-size: 14px; max-width: 75%; word-wrap: break-word;">
@if($msg['fileType'] === 'image' && $msg['fileUrl'])
<a href="{{ $msg['fileUrl'] }}" target="_blank"><img src="{{ $msg['fileUrl'] }}" alt="{{ $msg['fileName'] ?? 'Image' }}" style="max-width: 100%; max-height: 200px; border-radius: 8px; display: block; margin-bottom: 4px;"></a>
@elseif($msg['fileType'] === 'file' && $msg['fileUrl'])
<a href="{{ $msg['fileUrl'] }}" target="_blank" style="color: #fff; text-decoration: underline;">📎 {{ $msg['fileName'] ?? 'Download File' }}</a>
@else
{!! nl2br(e($msg['message'])) !!}
@endif
<div style="font-size: 10px; color: rgba(255,255,255,0.7); margin-top: 4px; text-align: right;">{{ $msg['time'] }}</div>
</div>
</div>

@elseif($msg['type'] === 'agent')
<div style="width: 100%; margin: 8px 0; text-align: left;">
<div style="font-size: 11px; color: #2b60d0; font-weight: bold; margin-bottom: 2px;">{{ $msg['sender'] }}</div>
<div style="display: inline-block; text-align: left; background-color: #f3f4f6; color: #1f2937; padding: 10px 15px; border-radius: 16px 16px 16px 4px; font-size: 14px; max-width: 75%; word-wrap: break-word;">
@if($msg['fileType'] === 'image' && $msg['fileUrl'])
<a href="{{ $msg['fileUrl'] }}" target="_blank"><img src="{{ $msg['fileUrl'] }}" alt="{{ $msg['fileName'] ?? 'Image' }}" style="max-width: 100%; max-height: 200px; border-radius: 8px; display: block; margin-bottom: 4px;"></a>
@elseif($msg['fileType'] === 'file' && $msg['fileUrl'])
<a href="{{ $msg['fileUrl'] }}" target="_blank" style="color: #2b60d0; text-decoration: underline;">📎 {{ $msg['fileName'] ?? 'Download File' }}</a>
@else
{!! nl2br(e($msg['message'])) !!}
@endif
<div style="font-size: 10px; color: #9ca3af; margin-top: 4px; text-align: right;">{{ $msg['time'] }}</div>
</div>
</div>

@else
<div style="width: 100%; margin: 8px 0; text-align: center;">
<span style="background-color: #f0fdf4; color: #166534; font-size: 12px; padding: 4px 12px; border-radius: 12px; border: 1px solid #bbf7d0; display: inline-block;">{!! nl2br(e($msg['message'])) !!}</span>
</div>
@endif

@endforeach
</div>
@else
No messages were found for this chat session.
@endif

Thanks,<br>
{{ $siteName }}
@endcomponent