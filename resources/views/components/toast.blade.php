    <div id="toast-container" class="toast-container" 
         data-success="{{ session('success') }}" 
         data-error="{{ session('error') }}" 
         data-info="{{ session('info') }}"
         data-errors="{{ $errors->any() ? json_encode($errors->all()) : '' }}"></div>
