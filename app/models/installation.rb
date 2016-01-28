class Installation < ActiveRecord::Base
  enum status: [:pushed, :downloaded, :cancelled, :installed, :wiped, :locked]
  delegate :app, to: :batch_installation, allow_nil: true

  belongs_to :device
  belongs_to :batch_installation

  after_create :push_apps, :wipe_apps, :lock_apps
  
  def as_json(options={})
    {
      :id => self.id,
      :name => self.app.name,
      :package_name => self.app.package_name,
      :apk_url => self.app.package_url
    }
  end

  def push_apps
    if self.pushed?
      gcm = GCM.new(GCM_KEY)
      registration_ids = [self.device.gcm_token]
      options = { data: {message: self.to_json, action:"push" }}
      response = gcm.send(registration_ids, options)
      logger.debug "response #{response}"
    end
  end
  
  def wipe_apps
    if self.wiped?
      gcm = GCM.new(GCM_KEY)
      registration_ids = [self.device.gcm_token]
      options = { data: {message: self.to_json, action:"wipe" }}
      response = gcm.send(registration_ids, options)
      logger.debug "response #{response}"
    end
  end
 
  def lock_apps
    if self.locked?
      gcm = GCM.new(GCM_KEY)
      registration_ids = [self.device.gcm_token]
      options = { data: {message: self.to_json, action:"lock" }}
      response = gcm.send(registration_ids, options)
      logger.debug "response #{response}"
    end
 end
end
