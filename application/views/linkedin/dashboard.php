<div id="dashboard_linkedin">
    
<div class="chrome" ng-app="LinkedInTalentSearchApp">

<div ng-controller="MainController">
    <div class="myContainer container">
        <div id="mySidebar" class="col-1 " ng-if="isShowSidebar">
            <header class="flex-container">
                <div class="create_list_wrap">
                    <input type="text" class="input_list" placeholder="+ Create a list" ng-model="data.groupTitle"><a class="new_list_btn" ng-click="addGroup()">+ Add</a>
                </div>
                <div id="chevron_left" style="flex-grow: 1; text-align: center;"><i class="fa fa-chevron-right"></i></div>
            </header>
            <h5 ng-if="groups.length == 0" style="margin-left: 20px">No groups. Please add new one.</h5>
            <table id="table_list" ng-if="groups.length > 0">
                <thead>
                    <tr class="table-list-head-row">
                        <th class="th_menu"></th>
                        <th class="th_title">List Title</th>
                        <%--<th class="th_profiles">Profiles</th>
                        <th class="th_updated">7 Day Check</th>
                        <th class="th_reporting">Newest Updates</th>--%>
                        <th class="th_select"></th>
                    </tr>
                </thead>
                <tbody id="table_list1">
                    <tr class="table_row" ng-repeat="group in groups" ng-class="{'selected': group.Id == selectedGroup.Id, 'weekly_checked_row': group.weeklyChecked}" ng-click="selectGroup(group)">
                        <td class="check_td">
                            <ul class="header-dropdown left-pop_menu m-r--5">
                                <li class="dropdown">
                                    <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="true">
                                        <i class="material-icons">more_vert</i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li><a ng-disabled="!group.IsEditable" ng-click="$event.stopPropagation();toggleReporting()" class=" waves-effect waves-block weekly_report_btn" ng-class="{'active': group.WeeklyReport}"><i class="material-icons">check_circle</i>Email me a weekly update</a><hr class="menu_hr">
                                        </li>
                                        <li><a ng-disabled="!group.IsEditable" ng-click="$event.stopPropagation();duplicateGroup()" class=" waves-effect waves-block duplicate_btn"><i class="material-icons">file_copy</i>Duplicate</a><hr class="menu_hr">
                                        </li>
                                        <li><a ng-disabled="!group.IsEditable" class="waves-effect waves-block  upload_link_btn" ngf-select="uploadCsv($files)" ngf-pattern="'.csv'" ngf-accept="'.csv'"><i class="material-icons">file_upload</i>Upload CSV</a></li>
                                        <li><a class="waves-effect waves-block  export_link_btn" ng-click="exportCsv(0)"><i class="material-icons">file_download</i>Export CSV - Recently updated</a></li>
                                        <li><a class="waves-effect waves-block  export_link_btn" ng-click="exportCsv(1)"><i class="material-icons">file_download</i>Export CSV - Full list</a></li>
                                        <li><a class="waves-effect waves-block  export_link_btn" ng-click="exportCsv(2)"><i class="material-icons">file_download</i>Export CSV - Companies</a><hr class="menu_hr">
                                        </li>
                                        <li><a ng-disabled="!group.IsEditable" ng-click="$event.stopPropagation();deleteGroup()" class=" waves-effect waves-block delete_btn"><i class="material-icons">delete</i>Delete</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </td>
                        <td class="list_title">
                            <input type="text" ng-model="group.Title" ng-disabled="!group.editingTitle" class="input_list_name" ng-click="$event.stopPropagation()" />
                            <i class="material-icons edit_btn" ng-click="$event.stopPropagation();editGroupTitle(group)" ng-if="group.IsEditable">edit</i>
                        </td>
                        <%--<td class="profile_count">{{group.ProfileCount}}</td>
                        <td class="created_time">{{group.UpdatedProfilesCount}}</td>
                        <td class="list_weekly">
                            <div class="list_weekly_container" ng-class="group.ChangeCount > 0 ? 'state_new' : 'state_inactive'">{{ (group.ChangeCount > 0 ? group.ChangeCount : 'no') + ' updates'}}</div>
                        </td>--%>
                        <td class="select_triangle"><i class="material-icons">double_arrow</i></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div id="main" class="" ng-if="selectedGroup">
            <section id="parameter-section">
                <div class="parameter">LinkedIn Talent Lists <i id="show-main" class="fa fa-chevron-down"></i></div>
                <header class="showing_list_title">{{selectedGroup.Title}}</header>
            </section>
            <header class="flex-container right-header">
                <div class="showing-container">
                    <!-- <a ng-click="$event.stopPropagation();showHideSidebar();" style="flex-grow: 0.02; cursor: pointer;">
                        <img ng-src="{{showHideImg.src}}" id="show_hide_sidebar_btn" />
                    </a> -->
                    <!-- <div class="showing_list_title" style="flex-grow: 2;">
                        {{selectedGroup.Title}} <span style="font-size: 14px">(Author: {{selectedGroup.Creator}})</span>
                    </div> -->
                    <div class="showing_list_export">
                        <button ng-click="$event.stopPropagation();downloadRecentChangePDF()" class="download export_link_btn" type="button" id="downloadRecentChangePDF" aria-haspopup="true" aria-expanded="false">
                            <i class="material-icons">file_download</i>PDF Recent Changes
                        </button>
                    </div>
                </div>
                <div class="content-container">
                    <span class="profile_count">{{selectedGroup.ProfileCount + ' profiles'}}</span>
                    <span class="created_time">{{selectedGroup.UpdatedProfilesCount + ' checked'}}</span>
                    <span class="list_weekly">
                        <div class="list_weekly_container" ng-class="selectedGroup.ChangeCount > 0 ? 'state_new' : 'state_inactive'">{{ (selectedGroup.ChangeCount > 0 ? selectedGroup.ChangeCount : 'no') + ' updates'}}</div>
                    </span>
                </div>
                <div id="chevron" style="flex-grow: 1; text-align: center;"><i class="fa fa-chevron-right"></i></div>
            </header>

            <div class="create_list_wrap create_list_right_wrap" ng-if="selectedGroup.IsEditable">
                <input type="text" class="input_linkedin_url" placeholder="+ Add new Linkedin URL" ng-model="data.profileUrl">
                <a class="add_link_btn" ng-click="addProfile()">+ Add</a>
            </div>

            <div class="function_wrap" ng-if="selectedGroup.profiles.length > 0">
                <div style="display: inline-block;margin-right: 5px;">
                    Sort
                    <span id="profiles_order" style="display: none"></span>
                </div>
                <select class="" id="sort">
                    <option value="name">Name</option>
                    <option value="company">Company</option>
                    <option value="update" selected>Update (newest first)</option>
                    <option value="title">Job Title</option>
                    <option value="employer">Employer</option>
                </select>
                <div class="sort_wrap" style="display:none">
                    <i class="material-icons sort" id="sort_icon"></i>
                </div>
                <div class="sort_wrap" style="margin-left: 15px;margin-right:5px">
                    Change Filter
                </div>
                <select ng-model="selectedGroup.changeFilter" ng-change="loadFilteredProfiles()">
                    <option value="">None</option>
                    <option value="1">Job Title</option>
                    <option value="2">Employer</option>
                </select>
                <span style="color: #797979; margin-left: 5px;">({{selectedGroup.filteredProfiles.length}})</span>
                <div class="inputContainer search_wrap ">
                    <input class="Field search_field" type="search" placeholder="Search">
                    <i class="close_search_btn material-icons">close</i>
                    <i id="search_btn" class="material-icons">search</i>
                </div>
            </div>

            <div class="function2_wrap" ng-if="selectedGroup.profiles.length > 0">
                <div class="date_filter_wrap">
                    <div class="date_filter">                                    
                        <md-datepicker ng-model="data.startDate" ng-change="loadFilteredProfiles()" md-placeholder="Start date"></md-datepicker>
                        <label>—</label>
                        <md-datepicker ng-model="data.endDate" ng-change="loadFilteredProfiles()" md-placeholder="End date"></md-datepicker>
                    </div>
                </div>
                <div class="download_list_report_btn" ng-click="downloadRecentChangePDF()"><i class="material-icons">file_download</i></div>
            </div>

            <h5 ng-if="selectedGroup.profiles && selectedGroup.profiles.length == 0" style="margin: 20px 0 0 20px;">No profiles for this group. Please add new one.</h5>
            <h5 ng-if="selectedGroup.profiles && selectedGroup.profiles.length > 0 && selectedGroup.filteredProfiles.length == 0" style="margin: 20px 0 0 20px;">No profiles matching filter.</h5>
            <table id="table" ng-if="selectedGroup.filteredProfiles && selectedGroup.filteredProfiles.length > 0">
                <thead>
                    <tr>
                        <!-- <th class="th_menu"></th> -->
                        <th class="th_name">Name</th>
                        <th class="th_company">Company</th>
                        <th class="th_position">Position</th>
                        <th class="th_updates">Updates</th>
                        <th class="th_state">State</th>
                        <th class="th_link">Link</th>
                    </tr>
                </thead>
                <tbody id="table1">
                    <tr class="table_row" ng-if="profile.ChangeCount > 0" ng-class="{'selected': profile.Id == selectedGroup.selectedProfile.Id}" ng-repeat="profile in selectedGroup.filteredProfiles" ng-click="selectedGroup.selectedProfile = profile">
                        <!-- <td class="check_td info_check_td">
                            <ul class="header-dropdown left-pop_menu m-r--5" ng-if="selectedGroup.IsEditable">
                                <li class="dropdown">
                                    <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="true">
                                        <i class="material-icons">more_vert</i>
                                    </a>
                                    <ul class="dropdown-menu ">
                                        <li><a ng-click="deleteProfile()" class=" waves-effect waves-block delete_btn delete"><i class="material-icons">delete</i>Delete</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </td> -->
                        <td class="name">
                            <div class="info_name">{{profile.Name}}</div>
                            <div class="info_location">{{profile.Location}}</div>
                        </td>
                        <td class="company_list">
                            <div class="company_list_icon" ng-click="$event.stopPropagation();openCompanyModal(profile);"></div>
                        </td>
                        <td class="position">
                            <div class="info_position match_position">{{profile.CurPosition}}</div>
                            <div class="info_company">{{profile.CurEmployer}}</div>
                            <div class="info_date">{{profile.CurDuration}}</div>
                        </td>
                        <td class="list_weekly">
                            <div class="list_weekly_container" ng-class="profile.ChangeCount > 0 ? 'state_has_new_update ' : 'state_no_update'">{{ (profile.ChangeCount > 0 ? profile.ChangeCount : 'no') + ' updates'}}</div>
                            <input class="update_sort" type="hidden" value="{{profile.SortVal}}" />
                        </td>
                        <td class="state state_{{profile.Status.toLowerCase()}}" ng-click="$event.stopPropagation();openDiffModal(profile)"><i class="material-icons">visibility</i> </td>
                        <td class="link"><a href="{{profile.Url}}" target="_blank"><i class="fa fa-linkedin" aria-hidden="true"></i></a></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div id="loading" ng-if="loading">
        <div class="loading-container">
            <label id="loading-label">{{ loadingText }}</label>
        </div>
    </div>

    <!-- The Modal -->
    <div class="modal fade" id="diffModal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header custom-modal-header" style="padding: 0 15px !important;">
                    <div class="row">
                        <div class="col-md-3 archive-area">
                            Versions Archive
                        </div>
                        <div class="col-md-9 diff-header">
                            <h4 class="modal-title">{{ selectedProfile.Name }}</h4>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <a href="{{selectedProfile.Url}}" target="_blank" style="float: right; margin-right: 20px"><i class="material-icons" style="margin-right: 15px; vertical-align: top; margin-top: 1px">open_in_new</i>{{ getSimpleUrl(selectedProfile.Url) }}</a>
                        </div>
                    </div>
                </div>
                <div class="modal-body" style="padding: 0 15px !important">
                    <div class="row">
                        <div class="col-md-3" style="padding: 2px 2px 0 2px; background-color: #e5e9ef;">
                            <div ng-repeat="diff in profileDiffs" class="version-item" ng-class="{'selected-diff': diff.newVer == selectedDiff.newVer}" ng-click="selectDiff(diff)">
                                <div class="profile-name">{{ selectedProfile.Name }}</div>
                                <div class="version-name">v. {{ diff.newVer.toFixed(1) }} from {{ diff.newDate | date:'dd:MM:yyyy' }}</div>
                            </div>
                        </div>
                        <div class="col-md-9" style="padding: 15px">
                            <table class="diff-table">
                                <tbody>
                                    <tr>
                                        <td class="field-title" style="background-color: #CACED9 !important">PREVIOUS PROFILE VERSION <span class="version-info">v. {{ selectedDiff.oldVer.toFixed(1) }} from {{ selectedDiff.oldDate | date:'dd:MM:yyyy' }}</span></td>
                                        <td class="middle-col"></td>
                                        <td class="field-title" style="background-color: #119558 !important; color: white !important;">NEW PROFILE UPDATES <span class="version-info" style="color: #084A2C">v. {{ selectedDiff.newVer.toFixed(1) }} from {{ selectedDiff.newDate | date:'dd:MM:yyyy' }}</span></td>
                                    </tr>
                                    <tr ng-repeat="change in selectedDiff.changes">
                                        <td>
                                            <h5 class="field-title" ng-if="change.Title.length > 0">{{ change.Title }}</h5>
                                            <div ng-bind-html="getHtml(change.OldContent)"></div>
                                        </td>
                                        <td class="middle-col"></td>
                                        <td>
                                            <h5 class="field-title" ng-if="change.Title.length > 0">{{ change.Title }}</h5>
                                            <div ng-bind-html="getHtml(change.NewContent)"></div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="companyModal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header custom-modal-header">
                    <h4 class="modal-title">{{ companyData.Name }}</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <a href="{{companyData.Url}}" target="_blank" style="float: right; margin-right: 20px"><i class="material-icons" style="margin-right: 15px; vertical-align: top; margin-top: 1px">open_in_new</i>{{ getSimpleUrl(companyData.Url) }}</a>
                </div>
                <div class="modal-body company-body">
                    <table id="table">
                        <tbody id="table1" class="modal-table-body">
                            <tr class="table_row selected" ng-repeat="profile in companyData.profiles">
                                <td class="name" style="">
                                    <div class="info_name" style="color: black !important">{{profile.Name}}</div>
                                    <div class="info_location" style="margin-top: 8px">{{profile.CurPosition}}</div>
                                </td>
                                <td class="position" style="">
                                    <div class="info_position match_position" ng-if="profile.Email && profile.Email.length > 0"><a href="mailto:{{profile.Email}}">{{profile.Email}}</a></div>
                                    <div class="info_date" style="margin-top: 5px">{{profile.Phone}}</div>
                                </td>
                                <td class="list_weekly" style="">
                                    <div class="list_weekly_container" ng-class="profile.ChangeCount > 0 ? 'state_has_update' : 'state_no_update'">{{ (profile.ChangeCount > 0 ? profile.ChangeCount : 'no') + ' updates'}}</div>
                                </td>
                                <td class="state" ng-class="'state_' + profile.Status.toLowerCase()" ng-click="$event.stopPropagation();openDiffModal(profile)"><i class="material-icons">visibility</i> </td>
                                <td class="link" style=""><a href="{{profile.Url}}" target="_blank"><i class="fa fa-linkedin" aria-hidden="true"></i></a></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="notification">
        <div class="modal-dialog">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header notification-header">
                    <h4 class="modal-title">Warning</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <!-- Modal body -->
                <div class="modal-body">
                    {{ notificationText }}
                </div>

                <!-- Modal footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-black" data-dismiss="modal">Close</button>
                </div>

            </div>
        </div>
    </div>

    <!-- <script type="text/javascript" src="<?= base_url() ?>assets/js/linkedin/Scripts/jquery.min.js"></script> -->
    <script type="text/javascript" src="<?= base_url() ?>assets/js/linkedin/Scripts/angular.min.js"></script>
    <script type="text/javascript" src="<?= base_url() ?>assets/js/linkedin/Scripts/ng-file-upload-shim.js"></script>
    <script type="text/javascript" src="<?= base_url() ?>assets/js/linkedin/Scripts/ng-file-upload.js"></script>
    <script type="text/javascript" src="<?= base_url() ?>assets/js/linkedin/Scripts/angular-sanitize.js"></script>
    <script type="text/javascript" src="<?= base_url() ?>assets/js/linkedin/Scripts/htmldiff.js"></script>

    <!-- Angular Material Dependencies -->
    <!-- <script type="text/javascript" src="<?= base_url() ?>assets/js/linkedin/Scripts/angular-animate.min.js"></script>
    <script type="text/javascript" src="<?= base_url() ?>assets/js/linkedin/Scripts/angular-aria.min.js"></script>
    <script type="text/javascript" src="<?= base_url() ?>assets/js/linkedin/Scripts/angular-messages.min.js"></script> -->
    
    <!-- Angular Material Javascript now available via Google CDN; version 1.2.1 used here -->
    <script type="text/javascript" src="<?= base_url() ?>assets/js/linkedin/Scripts/angular-material.min.js"></script>

    <script type="text/javascript" src="<?= base_url() ?>assets/js/linkedin/Scripts/script.js"></script>
    <script type="text/javascript" src="<?= base_url() ?>assets/js/linkedin/Scripts/main.js"></script>

    <!-- Bootstrap 4 dependency -->
    <!-- <script src="Scripts/bootstrap.min.js"></script> -->

    <!-- bootbox code -->
    <script src="<?= base_url() ?>assets/js/linkedin/Scripts/bootbox.min.js"></script>

</div>
</div>
</div>