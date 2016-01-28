ActiveAdmin.register App do
  #Hide sidebar Filters
  config.clear_sidebar_sections!
  permit_params :name, :package_name, :package_url
  #ACTIONS = ["push","wipe","lock"]

# See permitted parameters documentation:
# https://github.com/activeadmin/activeadmin/blob/master/docs/2-resource-customization.md#setting-up-strong-parameters
#
# permit_params :list, :of, :attributes, :on, :model
#
# or
#
# permit_params do
#   permitted = [:permitted, :attributes]
#   permitted << :other if resource.something?
#   permitted
# end
 form do |f|
   f.inputs do
 	 f.input :name
 	 f.input :package_name
   	 f.input :package_url
     #f.input :action, :label => 'Action', :as => :select, :collection => ACTIONS
   end
   f.actions
 end

end
