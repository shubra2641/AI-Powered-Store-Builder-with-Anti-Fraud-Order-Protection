<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Illuminate\Support\Str;

class Avatar extends Component
{
    public string $initials;
    public string $src;

    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $name = '',
        public int|string $size = 40,
        public ?string $image = null,
        public string $class = ''
    ) {
        $this->initials = Str::substr($name, 0, 1);
        
        if (!$image) {
            $backgrounds = ['8b5cf6', '3b82f6', '10b981', 'f59e0b', 'ef4444'];
            $bg = $backgrounds[ord(substr($name, 0, 1)) % count($backgrounds)];
            $this->src = "https://ui-avatars.com/api/?name=" . urlencode($name) . "&background=" . $bg . "&color=fff";
        } else {
            $this->src = $image;
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.avatar');
    }
}
