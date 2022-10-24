var app = angular.module('LinkedInTalentSearchApp', ['ngFileUpload', 'ngSanitize']);
app.controller('UserController', ['$scope', 'Upload', '$sce', '$filter', function ($scope, Upload, $sce, $filter) {

    $scope.loading = false;
    $scope.loadingText = "Loading...";
    $scope.notificationText = "";
    $scope.users = [];
    $scope.editUser = {};

    $scope.notify = function (text) {
        $scope.notificationText = text;
        $("#notification").modal("show");
    };

    $scope.changeStatus = function () {
        var users = $scope.users.filter(u => u.checked);
        if (users.length == 0) {
            $scope.notify("Please select users to change status.");
            return;
        }

        var formData = new FormData();
        formData.append("userIds", users.map(u => u.id).join("|"));
        var isActive = users.some(u => !u.isActive);
        formData.append("isActive", isActive ? '1' : '0');
        
        $scope.loadingText = "Updating user status...";
        $scope.loading = true;
        $.ajax({
            type: "POST",
            url: "Api/UpdateUserStatus.ashx",
            data: formData,
            contentType: false,
            processData: false,
            error: function (error) {
                $scope.loading = false;
                $scope.notify("Failed to update user status.");
            },
            success: function (res) {
                $scope.loading = false;
                if (res.message.length > 0) {
                    $scope.notify(res.message);
                }
                if (res.success) {
                    $scope.$apply(function () {
                        for (var user of $scope.users) {
                            if (users.some(u => u.id == user.id)) {
                                user.isActive = isActive;
                                user.checked = false;
                            }
                        }
                    })
                }
            }
        });
    };

    $scope.deleteUsers = function () {
        var users = $scope.users.filter(u => u.checked);
        if (users.length == 0) {
            $scope.notify("Please select users to delete.");
            return;
        }
        if (!confirm("Are you sure you want to delete this profile? Deleting user will remove his/her all groups and profiles.")) {
            return;
        }

        var formData = new FormData();
        formData.append("userIds", users.map(u => u.id).join("|"));

        $scope.loadingText = "Deleting users...";
        $scope.loading = true;
        $.ajax({
            type: "POST",
            url: "Api/DeleteUser.ashx",
            data: formData,
            contentType: false,
            processData: false,
            error: function (error) {
                $scope.loading = false;
                $scope.notify("Failed to delete user.");
            },
            success: function (res) {
                $scope.loading = false;
                if (res.message.length > 0) {
                    $scope.notify(res.message);
                }
                if (res.success) {
                    $scope.$apply(function () {
                        $scope.users = $scope.users.filter(u => !users.some(user => user.id == u.id));
                    })
                }
            }
        });
    };

    $scope.createUser = function () {
        $scope.userModalTitle = "Add new user";
        $scope.editUser = {
            isActive: true
        };
        $('#userModal').modal('show');
    };

    $scope.modifyUser = function (user) {
        $scope.userModalTitle = "Edit user";
        $scope.editUser = user;
        $('#userModal').modal('show');
    };

    $scope.saveUser = function () {
        if (!$scope.editUser.username) {
            $scope.notify("Username is required.");
            return;
        }
        if (!$scope.editUser.email) {
            $scope.notify("Email is required.");
            return;
        }

        if (!$scope.editUser.id) {
            if (!$scope.editUser.password) {
                $scope.notify("Password is required.");
                return;
            }
            if ($scope.editUser.password !== $scope.editUser.confirmPassword) {
                $scope.notify("Password is not matched.");
                return;
            }
        }

        var formData = new FormData();
        formData.append("user", JSON.stringify($scope.editUser));

        $scope.loadingText = "Saving user...";
        $scope.loading = true;
        $.ajax({
            type: "POST",
            url: "Api/SaveUser.ashx",
            data: formData,
            contentType: false,
            processData: false,
            error: function (error) {
                $scope.loading = false;
                $scope.notify("Failed to save user.");
            },
            success: function (res) {
                $scope.$apply(function () {
                    $scope.loading = false;
                    if (res.message.length > 0) {
                        $scope.notify(res.message);
                    }
                    if (res.success) {
                        if ($scope.editUser.id) {
                            for (var i in $scope.users) {
                                var user = $scope.users[i];
                                if (user.id == $scope.editUser.id) {
                                    $scope.users[i] = res.user;
                                    break;
                                }
                            }
                        } else {
                            $scope.users.push(res.user);
                        }

                        $('#userModal').modal('hide');
                    }
                });
            }
        });
    };

    var loadUsers = function () {
        $scope.loading = true;
        $.ajax({
            type: "GET",
            url: "Api/GetUsers.ashx",
            contentType: 'application/json; charset=utf-8',
            dataType: 'json',
            error: function (error) {
                $scope.loading = false;
                $scope.notify("Failed to load initial data.");
            },
            success: function (res) {
                $scope.$apply(function () {
                    if (res.success) {
                        $scope.users = res.users;
                    }
                    $scope.loading = false;
                });
            }
        });
    };

    loadUsers();
}]);