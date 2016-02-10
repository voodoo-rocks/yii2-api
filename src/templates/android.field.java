@SerializedName("{serialized-name}")
private {type} {field-name};

public function get{capitalized-field-name}() {
  return this.{field-name};
}

public function set{capitalized-field-name}({type} {field-name}) {
  this.{field-name} = {field-name};
}
