@if($isActive)
    @if($version === 'v3')
        <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">
        <script>
            grecaptcha.ready(function() {
                grecaptcha.execute("{{ $siteKey }}", {action: "submit"}).then(function(token) {
                    document.getElementById("g-recaptcha-response").value = token;
                });
            });
        </script>
    @else
        <div class="g-recaptcha my-4 w-full flex justify-center {{ $version === 'v2_invisible' ? 'g-recaptcha-invisible' : '' }}" 
             data-sitekey="{{ $siteKey }}"></div>
    @endif
@endif
