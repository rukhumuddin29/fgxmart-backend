<x-mail::message>
# Welcome to Fastkart, {{ $user->name }}!

Thank you for joining our community! We're excited to have you on board. At Fastkart, we strive to bring you the best selection of groceries and daily essentials right to your doorstep.

<x-mail::button :url="config('app.url')">
Start Shopping
</x-mail::button>

Happy Shopping,<br>
The Fastkart Team
@endcomponent

</x-mail::message>
