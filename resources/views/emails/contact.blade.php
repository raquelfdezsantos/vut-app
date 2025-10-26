<h2>Nueva consulta</h2>
<p><strong>Nombre:</strong> {{ $data['name'] }}</p>
<p><strong>Email:</strong> {{ $data['email'] }}</p>
<p><strong>Mensaje:</strong><br>{{ nl2br(e($data['message'])) }}</p>