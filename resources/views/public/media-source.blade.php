<div class="py-4">
	<h4 class="text-2xl font-bold mb-4">Kodi Settings</h4>

	<form action="/dmp-kodi/settings" method="post">
		@csrf
		@method('put')
		<div class="mb-5">
			<label for="kodi-server-url" class="block mb-2 font-bold"
				>Kodi Server URL</label
			>
			<input
				type="text"
				class="w-full mb-2"
				id="kodi-server-url"
				aria-describedby="kodi-server-urlHelp"
				name="kodi_url"
				value="{{ $options['kodi_url'] }}"
				required
			/>
			<div id="kodi-server-urlHelp" class="text-gray-400 text-sm">Ex: localhost, 10.0.0.32</div>
		</div>

		<div class="mb-5">
			<label for="kodi-server-port" class="block mb-2 font-bold"
				>Kodi Server Port</label
			>
			<input
				type="text"
				class="w-full mb-2"
				id="kodi-server-port"
				aria-describedby="kodi-server-portHelp"
				name="kodi_port"
				value="{{ $options['kodi_port'] }}"
				required
			/>
			<div id="kodi-server-portHelp" class="text-gray-400 text-sm"></div>
		</div>

		<div class="mb-5">
			<label for="kodi-socket-port" class="block mb-2 font-bold"
				>Kodi Socket Port</label
			>
			<input
				type="text"
				class="w-full mb-2"
				id="kodi-socket-port"
				aria-describedby="kodi-socket-portHelp"
				name="kodi_socket_port"
				value="{{ $options['kodi_socket_port'] }}"
			/>
			<div id="kodi-socket-portHelp" class="text-gray-400 text-sm"></div>
		</div>

		<div class="mb-5">
			<label for="kodi-username" class="block mb-2 font-bold"
				>Kodi Username</label
			>
			<input
				type="text"
				class="w-full mb-2"
				id="kodi-username"
				aria-describedby="kodi-usernameHelp"
				name="kodi_username"
				value="{{ $options['kodi_username'] }}"
			/>
			<div id="kodi-usernameHelp" class="text-gray-400 text-sm">Optional. Only if you have setup authentication.</div>
		</div>

		<div class="mb-5">
			<label for="kodi-password" class="block mb-2 font-bold"
				>Kodi Password</label
			>
			<input
				type="password"
				class="w-full mb-2"
				id="kodi-password"
				aria-describedby="kodi-passwordHelp"
				name="kodi_password"
				value="{{ $options['kodi_password'] }}"
			/>
			<div id="kodi-passwordHelp" class="text-gray-400 text-sm">Optional. Only if you have setup authentication.</div>
		</div>

		<button type="submit" class="btn-primary">Save</button>
	</form>
</div>
