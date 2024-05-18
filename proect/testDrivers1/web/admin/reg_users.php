<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация водителя</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        Регистрация водителя
                    </div>
                    <div class="card-body">
                        <form action="reg_users_process.php" method="POST">
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="form-group">
                                <label for="password">Пароль</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="form-group">
                                <label for="last_name">Фамилия</label>
                                <input type="text" class="form-control" id="last_name" name="last_name" required>
                            </div>
                            <div class="form-group">
                                <label for="first_name">Имя</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" required>
                            </div>
                            <div class="form-group">
                                <label for="middle_name">Отчество</label>
                                <input type="text" class="form-control" id="middle_name" name="middle_name">
                            </div>
                            <div class="form-group">
                                <label for="date_of_birth">Дата рождения</label>
                                <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" required>
                            </div>
                            <div class="form-group">
                                <label for="gender">Пол</label>
                                <select class="form-control" id="gender" name="gender" required>
                                    <option value="Male">Мужской</option>
                                    <option value="Female">Женский</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="phone_number">Номер телефона</label>
                                <input type="text" class="form-control" id="phone_number" name="phone_number">
                            </div>

                            <div class="form-group">
                                <label for="height">Рост (в см)</label>
                                <input type="number" step="any" class="form-control" id="height" name="height">
                            </div>
                            <div class="form-group">
                                <label for="weight">Вес (в кг)</label>
                                <input type="number" step="any" class="form-control" id="weight" name="weight">
                            </div>

                            <div class="form-group">
                                <label for="license_categories">Категории прав</label><br>
                       
                                <input type="checkbox" name="license_categories[]" value="1"> Легковой автомобиль<br>
                                <input type="checkbox" name="license_categories[]" value="2"> Грузовой автомобиль<br>
                                <input type="checkbox" name="license_categories[]" value="3"> Легковой автомобиль с прицепом<br>
                                <input type="checkbox" name="license_categories[]" value="4"> Грузовой автомобиль с прицепом<br>
                                
                            </div>

                            <button type="submit" class="btn btn-primary">Зарегистрировать</button>
                            <a href="admin_index.php" class="btn btn-secondary">Назад</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>