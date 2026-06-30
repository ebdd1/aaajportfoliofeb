<x-mail::message>
# Pesan Baru dari Portfolio

**Dari:** {{ $contactMessage->name }}
**Email:** {{ $contactMessage->email }}
**Waktu:** {{ $contactMessage->created_at->format('d M Y H:i') }}

---

**Pesan:**

{{ $contactMessage->message }}

---

<x-mail::button :url="url('/admin/messages/' . $contactMessage->id)" color="red">
Lihat di Dashboard
</x-mail::button>

© {{ date('Y') }} Portfolio Febryanus Tambing
</x-mail::message>
