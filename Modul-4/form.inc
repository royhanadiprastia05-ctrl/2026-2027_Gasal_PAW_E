
<form action="form.php" method="POST">
    <label>Surname:</label><br>
    <input type="text" name="surname"
        value="<?= htmlspecialchars($values['surname'] ?? '') ?>">
    <br><br>

    <label>Email address:</label><br>
    <input type="email" name="email"
        value="<?= htmlspecialchars($values['email'] ?? '') ?>">
    <br><br>

    <label>Password:</label><br>
    <input type="password" name="password">
    <br><br>

    <label>Street Address:</label><br>
    <textarea name="address"><?= htmlspecialchars($values['address'] ?? '') ?></textarea>
    <br><br>

    <label>State:</label><br>
    <select name="state">
        <option value="NSW">New South Wales</option>
        <option value="VIC">Victoria</option>
        <option value="QLD">Queensland</option>
        <option value="WA">Western Australia</option>
        <option value="SA">South Australia</option>
        <option value="TAS">Tasmania</option>
    </select>
    <br><br>

    <input type="hidden" name="country" value="Australia">

    <label>Gender:</label><br>
    <input type="radio" name="gender" value="Male"> Male
    <input type="radio" name="gender" value="Female"> Female
    <br><br>

    <label>Vegetarian?</label>
    <input type="checkbox" name="vegetarian" value="Yes">
    <br><br>

    <input type="submit" value="Submit">
    <input type="reset" value="Reset">
</form>