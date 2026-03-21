<div style="text-align: center; margin-top: 50px;">
    <h2>Connexion Administrateur</h2>
    <form action="{{ route('login.post') }}" method="POST">
        @csrf
        <input type="email" name="email" placeholder="Email" required><br><br>
        <input type="password" name="password" placeholder="Mot de passe" required><br><br>
        <button type="submit">Se connecter</button>
    </form>
</div>