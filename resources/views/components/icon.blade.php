@props([
  'name', // sun | moon | cookie
  'size' => 18,
  'class' => '',
  'weight' => 'thin' // thin|light|regular|bold|fill (guardamos thin por ahora)
])

@php
	$safeName = preg_replace('/[^a-z0-9\-]/', '', strtolower($name ?? ''));
	$safeWeight = preg_replace('/[^a-z]/', '', strtolower($weight ?? 'thin')) ?: 'thin';

	$iconPath = resource_path("icons/phosphor/{$safeWeight}/{$safeName}.svg");
	if (!file_exists($iconPath)) {
		$fallback = resource_path("icons/phosphor/thin/{$safeName}.svg");
		$iconPath = file_exists($fallback) ? $fallback : null;
	}

	$svg = $iconPath ? @file_get_contents($iconPath) : '';
	if ($svg) {
		// Ajustar tamaño: inyectar width/height al <svg>
		if (!preg_match('/\bwidth=/', $svg)) {
			$svg = preg_replace('/<svg\s+/i', '<svg width="'.$size.'" height="'.$size.'" ', $svg, 1);
		} else {
			$svg = preg_replace('/\bwidth="[^"]*"/i', 'width="'.$size.'"', $svg, 1);
			$svg = preg_replace('/\bheight="[^"]*"/i', 'height="'.$size.'"', $svg, 1);
		}
		// Forzar herencia de color: fill y stroke usan currentColor en el <svg>
		if (preg_match('/<svg[^>]*\bfill="/i', $svg)) {
			$svg = preg_replace('/(<svg\b[^>]*?)\bfill="[^"]*"/i', '$1 fill="currentColor"', $svg, 1);
		} else {
			$svg = preg_replace('/<svg(\s+)/i', '<svg$1 fill="currentColor" ', $svg, 1);
		}
		if (!preg_match('/stroke="/i', $svg)) {
			$svg = preg_replace('/<svg(\s+)/i', '<svg$1 stroke="currentColor" ', $svg, 1);
		}
		// Añadir class al svg raíz si se pasa desde props
		if (!empty($class)) {
			if (preg_match('/\bclass="/i', $svg)) {
				$svg = preg_replace('/class="([^"]*)"/i', 'class="$1 '.$class.'"', $svg, 1);
			} else {
				$svg = preg_replace('/<svg(\s+)/i', '<svg$1 class="'.$class.'" ', $svg, 1);
			}
		}
	}
@endphp

{!! $svg !!}

