// var app = angular.module('LinkedInTalentSearchApp', ['ngFileUpload', 'ngSanitize', 'ngMaterial', 'ngMessages']);
var app = angular.module('LinkedInTalentSearchApp', ['ngFileUpload']);
app.controller('MainController', ['$scope', 'Upload', '$sce', '$filter', function ($scope, Upload, $sce, $filter) {

    $scope.loading = false;
    $scope.loadingText = "Loading...";
    $scope.notificationText = "";
    $scope.companyData = {};
    $scope.profileDiffs = [];
    $scope.selectedDiff = {};
    $scope.groups = [];
    $scope.data = {
        groupTitle: null,
        profileUrl: null,
        selectedProfile: null,
        startDate: null,
        endDate: null
    };
    $scope.isShowSidebar = false;
    $scope.showHideImg = { src: "img/open.png" };

    $scope.notify = function (text) {
        $scope.notificationText = text;
        $("#notification").modal("show");
    };

    $scope.addGroup = function () {
        if (!$scope.data.groupTitle) {
            $scope.notify("Please input group title");
            return;
        }

        var formData = new FormData();
        formData.append("title", $scope.data.groupTitle);

        $scope.loadingText = "Adding group...";
        $scope.loading = true;
        $.ajax({
            type: "POST",
            url: base_url + "PublicApi/AddGroup.ashx",
            data: formData,
            contentType: false,
            processData: false,
            error: function (error) {
                $scope.loading = false;
                $scope.notify("Failed to add group");
            },
            success: function (res) {
                $scope.loading = false;
                if (res.message.length > 0) {
                    $scope.notify(res.message);
                }
                if (res.success) {
                    $scope.$apply(function () {
                        let group = res.group;
                        $scope.selectedGroup = group;
                        $scope.groups.push(group);
                        $scope.selectGroup(group);
                        $scope.data.groupTitle = '';
                    })
                }
            }
        });
    };

    $scope.selectGroup = function (group) {
        $scope.data.startDate = null;
        $scope.data.endDate = null;

        for (let g of $scope.groups) {
            if (g.editingTitle) {
                g.editingTitle = false;
                renameGroup(g)
            }
            g.changeFilter = null;
        }
        
        $scope.selectedGroup = group;
        window.selectedGroupId = group.id;
        $scope.showHideSidebar(true);
        if (!$scope.selectedGroup.profiles) {
            $scope.loadGroupProfiles();
        } else {
            $scope.selectedGroup.filteredProfiles = $scope.selectedGroup.profiles;
            if ($scope.selectedGroup.filteredProfiles.length > 0) {
                $scope.selectedGroup.selectedProfile = $scope.selectedGroup.filteredProfiles[0];
            }
            $scope.loading = false;
        }
    };

    $scope.loadGroupProfiles = function () {
        $scope.loadingText = "Loading group profiles...";
        $scope.loading = true;
        $.ajax({
            type: "GET",
            url: base_url + "PublicApi/GetGroupProfiles.ashx/" + $scope.selectedGroup.Id,
            contentType: 'application/json; charset=utf-8',
            dataType: 'json',
            error: function (error) {
                $scope.loading = false;
                $scope.notify("Failed to load group profiles.");
            },
            success: function (res) {
                $scope.$apply(function () {
                    if (res.success) {
                        for (var profile of res.profiles) {
                            profile.Status = $scope.getStatus(profile.HasChange, profile.UpdatedAt);
                        }
                        $scope.selectedGroup.profiles = res.profiles;
                        $scope.selectedGroup.filteredProfiles = res.profiles;
                        if (res.profiles.length > 0) {
                            $scope.selectedGroup.selectedProfile = $scope.selectedGroup.filteredProfiles[0];
                        }
                    }
                    $scope.loading = false;
                });
            }
        });
    };

    function renameGroup(group) {
        $.ajax({
            type: "POST",
            url: base_url + "PublicApi/RenameGroup.ashx/?GroupId=" + $scope.selectedGroup.Id + "&NewGroupName=" + group.Title,
            contentType: false,
            processData: false,
            error: function (error) {
                $scope.notify("Failed to delete group.");
            },
            success: function (result) {
                if (!result.success) {
                    $scope.notify("Failed to rename group.");
                }
            }
        });
    }

    $scope.editGroupTitle = function (group) {
        for (let g of $scope.groups) {
            if (g.Id != group.Id) {
                g.editingTitle = false;
            }
        }
        group.editingTitle = !group.editingTitle;

        if (group.editingTitle) return;

        renameGroup(group);
    };

    $scope.toggleReporting = function () {
        $scope.loadingText = "Toggling weekly report setting of group...";
        $scope.loading = true;
        $.ajax({
            type: "POST",
            url: base_url + "PublicApi/ToggleWeeklyReport.ashx/?GroupId=" + $scope.selectedGroup.Id,
            contentType: false,
            processData: false,
            error: function (error) {
                $scope.loading = false;
                $scope.notify("Failed toggling weekly report setting of group.");
            },
            success: function (result) {
                $scope.loading = false;
                if (result.success) {
                    $scope.$apply(function () {
                        $scope.selectedGroup.WeeklyReport = !$scope.selectedGroup.WeeklyReport;
                    })
                } else {
                    $scope.notify("Failed toggling weekly report setting of group.");
                }
            }
        });
    };

    $scope.duplicateGroup = function () {
        $scope.loadingText = "Duplicating profile...";
        $scope.loading = true;
        $.ajax({
            type: "GET",
            url: `Api/DuplicateGroup.ashx/?GroupId=${this.selectedGroup.Id}`,
            contentType: 'application/json; charset=utf-8',
            dataType: 'json',
            error: function (error) {
                console.log(error);
                $scope.loading = false;
                $scope.notify("Failed to duplicate group");
            },
            success: function (result) {
                $scope.loading = false;
                if (result.message.length > 0) {
                    $scope.notify(result.message);
                }
                if (result.success) {
                    $scope.$apply(function () {
                        $scope.groups.push(result.group);
                    });
                }
            }
        });
    };

    $scope.deleteGroup = function () {
        if (!confirm("Are you sure you want to delete this group and its all profiles?")) {
            return;
        }

        $scope.loadingText = "Deleting group...";
        $scope.loading = true;
        $.ajax({
            type: "POST",
            url: base_url + "PublicApi/DeleteGroup.ashx/?GroupId=" + $scope.selectedGroup.Id,
            contentType: false,
            processData: false,
            error: function (error) {
                $scope.loading = false;
                $scope.notify("Failed to delete group.");
            },
            success: function (result) {
                $scope.loading = false;
                if (result.success) {
                    $scope.$apply(function () {
                        $scope.groups = $scope.groups.filter(g => g.Id != $scope.selectedGroup.Id);
                        if ($scope.groups.length > 0) {
                            $scope.selectGroup($scope.groups[0]);
                        } else {
                            $scope.selectedGroup = null;
                        }
                    })
                } else {
                    $scope.notify("Failed to delete group.");
                }
            }
        });
    };

    $scope.uploadCsv = function (files) {
        if (files.length > 0) {
            var formData = new FormData();
            formData.append("file", files[0], files[0].name);
            formData.append("groupId", $scope.selectedGroup.Id);

            $scope.loadingText = "Uploading csv...";
            $scope.loading = true;
            $.ajax({
                type: "POST",
                url: base_url + "PublicApi/UploadCsv.ashx",
                success: function (data) {
                    $scope.loading = false;
                    if (data.message.length > 0) {
                        $scope.notify(data.message);
                    }
                    if (data.success) {
                        $scope.$apply(function () {
                            for (let profile of data.profiles) {
                                $scope.selectedGroup.profiles.push(profile);
                            }
                            if ($scope.selectedGroup.profiles.length == data.profiles.length) {
                                $scope.selectedGroup.selectedProfile = $scope.selectedGroup.profiles[0];
                            }
                        });
                    }
                },
                error: function (error) {
                    console.log(error);
                    $scope.loading = false;
                    $scope.notify("Failed to upload csv.");
                },
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                timeout: -1
            });
        }
    };

    $scope.exportCsv = function (exportType) {
        if (this.selectedGroup.profiles.length == 0) {
            $scope.notify("No profile to export.");
            return;
        }

        var url = `${window.location.origin}/Api/ExportCsv.ashx?GroupId=${this.selectedGroup.Id}&GroupName=${this.selectedGroup.Title}&ExportType=${exportType}`;
        window.open(url, "_blank");
    };

    $scope.formatDate = function (dateVal, divider = "/") {
        var strDate = dateVal.getFullYear() + divider;
        var month = dateVal.getMonth() + 1;
        if (month < 10) {
            month = "0" + month;
        }
        strDate += month + divider;
        var date = dateVal.getDate();
        if (date < 10) {
            date = "0" + date;
        }
        strDate += date;

        return strDate;
    };

    $scope.downloadRecentChangePDF = function () {
        if (this.selectedGroup.profiles.length == 0) {
            $scope.notify("No profile changes to download.");
            return;
        }

        var startDate = '';
        if ($scope.data.startDate) {
            startDate = $scope.formatDate($scope.data.startDate, '-');
        }
        var endDate = '';
        if ($scope.data.endDate) {
            endDate = $scope.formatDate($scope.data.endDate, '-');
        }
        var url = `${talent_lib_server}/PublicApi/DownloadRecentChagnesAsPDF.ashx?GroupId=${this.selectedGroup.Id}&GroupName=${this.selectedGroup.Title}&startDate=${startDate}&endDate=${endDate}`;
        window.open(url, "_blank");
    };

    $scope.addProfile = function () {
        if (!$scope.selectedGroup.IsEditable) {
            $scope.notify("Permission denied.");
            return;
        }

        if (!$scope.data.profileUrl) {
            $scope.notify("Please input profile url.");
            return;
        }

        var formData = new FormData();
        formData.append("groupId", $scope.selectedGroup.Id);
        formData.append("url", $scope.data.profileUrl);

        $scope.loadingText = "Adding profile...";
        $scope.loading = true;
        $.ajax({
            type: "POST",
            url: base_url + "PublicApi/AddProfile.ashx",
            data: formData,
            contentType: false,
            processData: false,
            error: function (error) {
                $scope.loading = false;
                $scope.notify("Failed to add profile.");
            },
            success: function (res) {
                $scope.loading = false;
                if (res.message.length > 0) {
                    console.log(res.message);
                    $scope.notify(res.message);
                }
                if (res.success) {
                    $scope.$apply(function () {
                        let profile = res.profile;
                        $scope.selectedGroup.profiles.push(profile);
                        $scope.selectedGroup.selectedProfile = profile;
                        $scope.data.profileUrl = '';
                    })
                }
            }
        });
    };

    $scope.deleteProfile = function () {
        if (!confirm("Are you sure you want to delete this profile?")) {
            return;
        }

        $scope.loadingText = "Deleting profile...";
        $scope.loading = true;
        $.ajax({
            type: "POST",
            url: base_url + "PublicApi/DeleteProfile.ashx/?ProfileId=" + $scope.selectedGroup.selectedProfile.Id,
            contentType: false,
            processData: false,
            error: function (error) {
                $scope.loading = false;
                $scope.notify("Failed to delete profile.");
            },
            success: function (result) {
                $scope.loading = false;
                if (result.success) {
                    $scope.$apply(function () {
                        $scope.selectedGroup.profiles = $scope.selectedGroup.profiles.filter(p => p.Id != $scope.selectedGroup.selectedProfile.Id);
                        if ($scope.selectedGroup.profiles.length > 0) {
                            $scope.selectedGroup.selectedProfile = $scope.selectedGroup.profiles[0];
                        } else {
                            $scope.selectedGroup.selectedProfile = null;
                        }
                    })
                } else {
                    $scope.notify("Failed to delete profile.");
                }
            }
        });
    };

    $scope.openDiffModal = function (profile) {
        if (!profile.HasChange) {
            $scope.notify("The profile has no change to display");
            return;
        }

        $scope.selectedProfile = profile;
        $scope.loadingText = "Fetching profile change...";
        $scope.loading = true;
        $.ajax({
            type: "POST",
            url: base_url + "PublicApi/GetProfileDiff.ashx/" + profile.Id,
            contentType: false,
            processData: false,
            error: function (error) {
                $scope.loading = false;
                $scope.notify("Failed to get profile changes.");
            },
            dataType: 'json',
            success: function (result) {
                $scope.loading = false;
                if (result.message != "") {
                    $scope.notify(result.message);
                }
                if (result.success) {
                    $scope.$apply(function () {
                        var diffs = [];
                        for (var i in result.changes) {
                            var change = result.changes[i];
                            var oldVer = i == result.changes.length - 1 ? '0.0' : result.changes[parseInt(i) + 1].Version;
                            var oldDate = i == result.changes.length - 1 ? profile.CreatedAt : result.changes[parseInt(i) + 1].CreatedAt;
                            var diffItem = {
                                oldVer,
                                oldDate,
                                newVer: change.Version,
                                newDate: change.CreatedAt,
                                changes: []
                            };
                            for (var diff of change.diffs) {
                                diffItem.changes.push({
                                    Title: diff.Title,
                                    OldContent: diff.OldContent,
                                    NewContent: htmldiff(diff.OldContent, diff.NewContent)
                                });
                            }
                            diffs.push(diffItem);
                        }

                        $scope.profileDiffs = diffs;
                        $scope.selectedDiff = diffs[0];
                        $('#diffModal').modal('show');
                    });
                }
            }
        });
    };

    $scope.getStatus = function (hasChange, checkedAt) {
        if (!hasChange) {
            return checkedAt ? "Active" : "";
        }

        var checkedTime = new Date(checkedAt);
        var now = new Date();
        var diffDays = (now.getTime() - checkedTime.getTime()) / 1000 / 86400;
        if (diffDays <= 10) {
            return "Recent";
        } else if (diffDays <= 30) {
            return "New";
        } else {
            return "Old";
        }
    };

    $scope.getHtml = function (htmlStr) {
        return $sce.trustAsHtml(htmlStr);
    };

    $scope.getWeeklyChecked = function (checkedAt) {
        if (!checkedAt) {
            return false;
        }

        var numberCurrentDateWeeks = $filter('date')(new Date(), "w");
        var numberCheckedDateWeeks = $filter('date')(checkedAt, "w");

        return numberCurrentDateWeeks == numberCheckedDateWeeks;
    };

    $scope.openCompanyModal = function (profile) {
        if (!profile.CompanyId || profile.CompanyId.length == 0) {
            $scope.notify("The profile is not associated to any company");
            return;
        }

        $scope.loadingText = "Fetching company profiles...";
        $scope.loading = true;
        $.ajax({
            type: "POST",
            url: base_url + "PublicApi/GetCompanyProfiles.ashx/" + profile.CompanyId + "/" + $scope.selectedGroup.Id,
            contentType: false,
            processData: false,
            error: function (error) {
                $scope.loading = false;
                $scope.notify("Failed to get company profiles.");
            },
            dataType: 'json',
            success: function (result) {
                $scope.loading = false;
                if (result.message != "") {
                    $scope.notify(result.message);
                }
                if (result.success) {
                    $scope.$apply(function () {
                        $scope.companyData = result.data;
                        for (var profile of $scope.companyData.profiles) {
                            profile.Status = $scope.getStatus(profile.HasChange, profile.UpdatedAt);
                        }
                        $('#companyModal').modal('show');
                    });
                }
            }
        });
    };

    $scope.getSimpleUrl = function (url) {
        if (!url) {
            return '#';
        }

        return url.replace("https://www.", "").replace("https://", "");
    };

    $scope.selectDiff = function (diff) {
        $scope.selectedDiff = diff;
    };

    $scope.loadFilteredProfiles = function () {
        if ($scope.selectedGroup.changeFilter == 1) {
            $scope.selectedGroup.filteredProfiles = $scope.selectedGroup.profiles.filter(x => x.TitleChangeCount > 0);
        } else if ($scope.selectedGroup.changeFilter == 2) {
            $scope.selectedGroup.filteredProfiles = $scope.selectedGroup.profiles.filter(x => x.EmpChangeCount > 0);
        } else {
            $scope.selectedGroup.filteredProfiles = $scope.selectedGroup.profiles;
        }

        if ($scope.data.startDate) {
            $scope.selectedGroup.filteredProfiles = $scope.selectedGroup.filteredProfiles.filter(x => new Date(x.UpdatedAt) >= $scope.data.startDate);
        }

        if ($scope.data.endDate) {
            $scope.selectedGroup.filteredProfiles = $scope.selectedGroup.filteredProfiles.filter(x => new Date(x.UpdatedAt) <= $scope.data.endDate);
        }

        if ($scope.selectedGroup.filteredProfiles.length > 0) {
            $scope.selectedGroup.selectedProfile = $scope.selectedGroup.filteredProfiles[0];
        }
        $scope.$applyAsync();
    };

    $scope.showHideSidebar = function (forceClose = false) {
        if ($scope.isShowSidebar == true || forceClose) {
            $scope.showHideImg.src = "img/open.png";
            $scope.isShowSidebar = false;
            $("#main").css('margin-left', '25%');
        } else {
            $scope.showHideImg.src = "img/close.png";
            $scope.isShowSidebar = true;
            $("#main").css('margin-left', '0');
        }
    };

    var loadGroups = function () {
        $scope.loading = true;
        $.ajax({
            type: "GET",
            url: base_url + "PublicApi/GetGroups.ashx",
            contentType: 'application/json; charset=utf-8',
            dataType: 'json',
            error: function (error) {
                $scope.loading = false;
                $scope.notify("Failed to load initial data.");
            },
            success: function (res) {
                $scope.$apply(function () {
                    if (res.success) {
                        $scope.groups = res.groups;
                        for (var group of $scope.groups) {
                            group.weeklyChecked = $scope.getWeeklyChecked(group.UpdatedOn);
                            group.Status = $scope.getStatus(group.HasChange, group.UpdatedOn);
                        }
                        if ($scope.groups.length > 0) {
                            $scope.selectGroup($scope.groups[0]);
                        }
                        else {
                            $scope.loading = false;
                        }
                    }
                    else {
                        $scope.loading = false;
                    }
                });
            }
        });
    };

    loadGroups();
}]);