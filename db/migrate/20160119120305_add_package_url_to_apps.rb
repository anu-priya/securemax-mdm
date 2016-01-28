class AddPackageUrlToApps < ActiveRecord::Migration
  def change
  	add_column :apps, :package_url, :string
	#add_column :apps, :action, :string
  end
end
